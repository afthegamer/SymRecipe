<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Message des contacts')
            ->setEntityLabelInSingular('Message de contact')
            ->setPageTitle('index',"administration des demande de contact")
            ->setPaginatorPageSize(5)
//            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
            ;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnIndex()
                ->setFormTypeOption('disabled','disabled'),
            TextField::new('fullName')
                ->setFormTypeOption('disabled','disabled'),
            TextField::new('email')
                ->setFormTypeOption('disabled','disabled'),
            TextEditorField::new('message')
//                ->setFormType(CKEditorType::class)
                ->hideOnIndex()
                ->setFormTypeOption('disabled','disabled'),
            DateTimeField::new('createdAt')
                ->setFormTypeOption('disabled','disabled'),
        ];
    }

}
