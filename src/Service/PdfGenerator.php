<?php

namespace App\Service;

use App\Entity\Societe;
use App\Entity\Associate;
use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

class PdfGenerator
{
    public function __construct(
        private readonly Environment $twig,
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }
    private function configureDompdf(): Dompdf
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);

        return new Dompdf($options);
    }
    public function generateSocietePdf(string $template, Societe $societe): string
    {
        $dompdf = $this->configureDompdf();
        $dataSociete = $this->serializer->normalize($societe);

        $html = $this->twig->render($template, $dataSociete);

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
    public function generateAssociatePdf(string $template, Associate $associate): string
    {
        $dompdf = $this->configureDompdf();
        $dataAssociate = $this->serializer->normalize($associate);

        $html = $this->twig->render($template, $dataAssociate);

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    public function generateTypeSocietePdf(
        string $template,
        Document $document,
        Societe $societe,
    ): string {
        $dompdf = $this->configureDompdf();

        // Collecte automatique des placeholders dynamiques
        $placeholders = [];
        $reflectionClass = new \ReflectionClass(Societe::class);

        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (strpos($method->getName(), 'get') === 0) {
                $placeholderKey = '{' . $method->getName() . '?}';
                $value = $method->invoke($societe);

                if($value instanceof \DateTimeInterface) {
                    $value = $value->format('d/m/Y');
                }
                // Gestion des images encodées en Base64
                if (is_string($value) && str_starts_with($value, 'data:image/')) {
                    // Remplacez par un tag <img>
                    $imagePath = $this->saveBase64ImageToTempFile($value);

                    $value = '<img src="' . $value . '" alt="Image" style="max-width: 100%; height: auto;">';
                }
                // Générer un contenu structuré pour les associés
                $associatesHtml = '<ul>';
                //conversion des collections
                if($value instanceof \Doctrine\Common\Collections\Collection) {
                    $associateData = [];
                    $associates = $this->entityManager
                        ->getRepository(Associate::class)
                        ->findBy(['societe' => $societe->getId()]);
                    $index = 1;

                    foreach($associates as $associate) {
                        //debut
                        $associatesHtml .= '<li><strong><u>Associé ' . $index . '</u>:</strong><br>';
                        $associatesHtml .= sprintf('%s %s ', $associate->getCivility(), $associate->getLastname());
                        $associatesHtml .= sprintf('%s ', $associate->getFirstname());
                        $associatesHtml .= sprintf(', <i>demeurant</i> : %s, %s, %s', $associate->getAddress(), $associate->getCity(), $associate->getCountry());
                        $associatesHtml .= sprintf(', <i>de nationalité</i> : %s', $associate->getNationality());
                        $associatesHtml .= sprintf(
                            ', <i>Né(e) le :</i> %s<br>',
                            $associate->getDateOfBirth() instanceof \DateTimeInterface
                                ? $associate->getDateOfBirth()->format('d/m/Y')
                                : ''
                        );
                        $associatesHtml .= '</li>';
                        $index++;
                        //fin
                    }

                    $associatesHtml .= '</ul>';
                    // Ajouter les associés au tableau des placeholders
                    $placeholders['{getAssociates?}'] = $associatesHtml;
                    //$placeholders[$placeholderKey] = json_encode($associateData);
                }else{
                    $placeholders[$placeholderKey] = $value;
                }
            }
        }

        $content = [
            'title' => $document->getTitle(),
            'paragraphe' => str_replace(
                array_keys($placeholders),
                array_values($placeholders),
                $document->getParagraphe()
            ),
            'paragraphes' => [],
        ];

        foreach ($document->getParagraphes() as $paragraphe) {
            $content['paragraphes'][] = str_replace(
                array_keys($placeholders),
                array_values($placeholders),
                $paragraphe
            );
        }
        // Rendu du template avec les données

        $html = $this->twig->render($template, $content);

        // Convertir l'encodage pour Dompdf
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
    /**
     * Enregistre une image encodée en Base64 dans un fichier temporaire.
     *
     * @param string $base64Image Données de l'image en Base64.
     * @return string Chemin du fichier temporaire enregistré.
     */
    private function saveBase64ImageToTempFile(string $base64Image): string
    {
        // Extraire les données de l'image
        preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches);
        $extension = $matches[1];
        $data = substr($base64Image, strpos($base64Image, ',') + 1);
        $data = base64_decode($data);

        // Générer un fichier temporaire
        $tempDir = sys_get_temp_dir();
        $tempFile = tempnam($tempDir, 'image_') . '.' . $extension;
        file_put_contents($tempFile, $data);

        return $tempFile;
    }
}