<?php

namespace VdubDev\CsvPreviewConvert\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use VdubDev\CsvPreviewConvert\Enum\CharsetEnum;
use VdubDev\CsvPreviewConvert\Form\CsvImportType;
use VdubDev\CsvPreviewConvert\Service\CsvPreviewConvertManager;
use VdubDev\CsvPreviewConvert\Service\CsvPreviewConvertSessionManager;

#[Route('/csv-preview', name: 'csv_preview')]
class CsvPreviewConvertController extends AbstractController
{
    #[Route('-popup', name: '_popup', methods: ['GET'])]
    public function popup(
        CsvPreviewConvertManager $csvPreviewConvertManager): Response
    {
        $csvPreviewConvertManager->cleanOldFiles();

        $form = $this->createForm(CsvImportType::class);

        return $this->render('@CsvPreviewConvert/csv_preview_convert_popup.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('-submit', name: '_submit', methods: ['POST'])]
    public function submit(
        Request $request,
        CsvPreviewConvertManager $csvPreviewConvertManager,
        CsvPreviewConvertSessionManager $csvPreviewConvertSessionManager,
    ): Response {
        $encodings = CharsetEnum::cases();

        /** @var string[]|null $data */
        $data = json_decode($request->getContent(), true);
        $actionType = $data['action_type'] ?? 'form_submit';
        $selectedEncoding = $data['encoding'] ?? 'UTF-8';

        $selectedEncoding = CharsetEnum::normalize($selectedEncoding);

        $canGeneratePreview = false;

        $form = $this->createForm(CsvImportType::class);
        $form->handleRequest($request);

        if ($actionType === 'form_submit') {

            if ($form->isSubmitted() && $form->isValid()) {

                $csvFile = $form->get('file')->getData();

                if ($csvFile instanceof UploadedFile) {
                    $storedPath = $csvPreviewConvertManager->saveUploadedFile($csvFile);
                    $csvPreviewConvertSessionManager->setCurrentCsvPath($storedPath);
                    $canGeneratePreview = true;
                }
            }
        }

        if ($actionType === 'encoding_change') {
            // Récupère le chemin du CSV stocké en session
            $currentCsvPath = $csvPreviewConvertSessionManager->getCurrentCsvPath();

            if ($currentCsvPath && file_exists($currentCsvPath) && $csvPreviewConvertSessionManager->hasValidCsv()) {
                $canGeneratePreview = true;
            }
        }

        return $this->render('@CsvPreviewConvert/includes/_csv_preview_convert_popup_content.html.twig', [
            'form' => $form,
            'encodings' => $encodings,
            'selectedEncoding' => $selectedEncoding,
            'canGeneratePreview' => $canGeneratePreview,
        ]);
    }

    #[Route('-content', name: '_content', methods: ['GET'])]
    public function content(
        Request $request,
        CsvPreviewConvertManager $csvPreviewConvertManager,
        CsvPreviewConvertSessionManager $csvPreviewConvertSessionManager,
    ): Response {
        /** @var string $selectedEncoding */
        $selectedEncoding = $request->query->get('encoding', 'UTF-8');

        $selectedEncoding = CharsetEnum::normalize($selectedEncoding);

        $preview = [];
        $currentCsvPath = $csvPreviewConvertSessionManager->getCurrentCsvPath();

        if ($currentCsvPath && file_exists($currentCsvPath) && $csvPreviewConvertSessionManager->hasValidCsv()) {
            $preview = $csvPreviewConvertManager->previewFile($currentCsvPath);
        }

        return new Response(
            $this->renderView('@CsvPreviewConvert/includes/_csv_preview_content.html.twig', [
                'selectedEncoding' => strtolower($selectedEncoding),
                'preview' => $preview,
            ]),
            200,
            ['Content-Type' => 'text/html; charset=' . strtolower($selectedEncoding)]
        );
    }

    #[Route('-convert-download', name: '_convert_and_download', methods: ['POST'])]
    public function download(
        Request $request,
        CsvPreviewConvertManager $csvPreviewConvertManager,
        CsvPreviewConvertSessionManager $csvPreviewConvertSessionManager,
    ): Response {
        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token', '');

        if (!$this->isCsrfTokenValid('csv-convert-download', $csrfToken)) {
            return new JsonResponse([
                'error' => 'The CSRF token is invalid.',
            ], 422);
        }

        /** @var string $fromEncoding */
        $fromEncoding = $request->request->get('encoding', 'UTF-8');

        $fromEncoding = CharsetEnum::normalize($fromEncoding);

        /** @var string $toEncoding */
        $toEncoding = $request->request->get('output_encoding', 'UTF-8');

        $toEncoding = CharsetEnum::normalize($toEncoding);

        $currentCsvPath = $csvPreviewConvertSessionManager->getCurrentCsvPath();

        if ($currentCsvPath && file_exists($currentCsvPath) && $csvPreviewConvertSessionManager->hasValidCsv()) {
            $convertedContent = $csvPreviewConvertManager->convertEncoding($currentCsvPath, $fromEncoding, $toEncoding);

            $response = new Response($convertedContent);

            $fileInfo = pathinfo($currentCsvPath);
            $originalName = $fileInfo['filename'];
            $extension = 'csv';
            $outputFileName = sprintf('%s_%s-to-%s.%s', $originalName, $fromEncoding, $toEncoding, $extension);

            // Définir les headers
            $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $outputFileName
            );
            $response->headers->set('Content-Disposition', $disposition);

            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');

            return $response;
        }

        return new Response('Fichier introuvable', 404);
    }
}
