<?php

namespace VdubDev\CsvPreviewConvert\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Form type for importing a CSV file.
 *
 * This form provides a single 'file' field allowing users to upload a CSV file.
 * It includes validation for required field, file size, MIME type, and file extension.
 *
 * CSRF protection is enabled to prevent cross-site request forgery.
 */
class CsvImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Fichier CSV à importer',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un fichier CSV.',
                    ]),
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                            'application/csv',
                            'application/vnd.ms-excel',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier valide au format CSV.',
                    ]),
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        if (!$value instanceof UploadedFile) {
                            return;
                        }
                        $name = $value->getClientOriginalName();
                        if (!preg_match('/\.csv$/i', $name)) {
                            $context->addViolation('Le fichier doit avoir une extension .csv');
                        }
                    }),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'csv_preview',
        ]);
    }
}
