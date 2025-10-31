<?php

declare(strict_types=1);

namespace DDNSBundle\Controller;

use DDNSBundle\Entity\ExampleEntity;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

#[AdminCrud(
    routePath: '/ddns/example-entity',
    routeName: 'ddns_example_entity'
)]
final class ExampleEntityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ExampleEntity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('DDNS服务器节点')
            ->setEntityLabelInPlural('DDNS服务器节点管理')
            ->setPageTitle(Crud::PAGE_INDEX, 'DDNS服务器节点列表')
            ->setPageTitle(Crud::PAGE_NEW, '新建DDNS服务器节点')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑DDNS服务器节点')
            ->setPageTitle(Crud::PAGE_DETAIL, 'DDNS服务器节点详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'domain', 'ipAddress'])
            ->showEntityActionsInlined()
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
        ;

        yield TextField::new('name', '服务器名称')
            ->setColumns('col-md-4')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('服务器的友好名称，用于标识和管理')
        ;

        yield TextField::new('domain', '域名')
            ->setColumns('col-md-4')
            ->setRequired(true)
            ->setMaxLength(255)
            ->setHelp('服务器的域名，带有DDNSDomain属性，会自动同步到Cloudflare')
        ;

        yield TextField::new('ipAddress', 'IP地址')
            ->setColumns('col-md-4')
            ->setRequired(true)
            ->setMaxLength(45)
            ->setHelp('服务器的IP地址，带有DdnsIp属性，支持IPv4和IPv6')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name'))
            ->add(TextFilter::new('domain'))
            ->add(TextFilter::new('ipAddress'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye')->setLabel('查看');
            })
            ->setPermissions([
                Action::INDEX => 'ROLE_ADMIN',
                Action::DETAIL => 'ROLE_ADMIN',
                Action::EDIT => 'ROLE_ADMIN',
                Action::NEW => 'ROLE_ADMIN',
                Action::DELETE => 'ROLE_ADMIN',
            ])
        ;
    }
}
