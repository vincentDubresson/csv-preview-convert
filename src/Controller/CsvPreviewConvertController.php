<?php

namespace VdubDev\CsvPreviewConvert\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use VdubDev\CsvPreviewConvert\Form\CsvImportType;

#[Route('/_csv_preview_convert', name: '_csv_preview_convert')]
class CsvPreviewConvertController extends AbstractController
{
    #[Route('_popup', name: '_popup', methods: ['GET'])]
    public function popup(Request $request): Response
    {
        $form = $this->createForm(CsvImportType::class);

        return $this->render('@CsvPreviewConvert/csv_preview_convert_popup.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('_preview', name: '_preview', methods: ['POST'])]
    public function preview(Request $request): Response
    {
        $form = $this->createForm(CsvImportType::class);
        $form->handleRequest($request);

        return $this->render('@CsvPreviewConvert/includes/_csv_preview_convert_popup_content.html.twig', [
            'form' => $form,
        ]);
    }
}
