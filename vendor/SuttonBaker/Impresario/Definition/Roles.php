<?php

namespace SuttonBaker\Impresario\Definition;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Definition
 */
class Roles
{
    const ROLE_PROJECT_MANAGER = 'project_manager';
    const ROLE_ENGINEER = 'engineer';
    const ROLE_FOREMAN = 'foreman';
    const ROLE_ESTIMATOR = 'estimator';
    const ROLE_ADMINISTRATOR = 'administrator'; // Use WP's administrator

    const CAP_EDIT_CLIENT = 'edit_client';
    const CAP_VIEW_CLIENT = 'view_client';
    const CAP_EDIT_ENQUIRY = 'edit_enquiry';
    const CAP_VIEW_ENQUIRY = 'view_enquiry';
    const CAP_EDIT_QUOTE = 'edit_quote';
    const CAP_VIEW_QUOTE = 'view_quote';
    const CAP_VIEW_PROJECT = 'view_project';
    const CAP_EDIT_PROJECT = 'edit_project';
    const CAP_EDIT_TASK = 'edit_task';
    const CAP_VIEW_TASK = 'view_task';
    const CAP_VIEW_VARIATION = 'view_variation';
    const CAP_EDIT_VARIATION = 'edit_variation';
    const CAP_VIEW_INVOICE = 'view_invoice';
    const CAP_EDIT_INVOICE = 'edit_invoice';
    const CAP_ALL = 'all';

    /**
     * @return array
     */
    public static function getRoles()
    {
        return [
            self::ROLE_PROJECT_MANAGER => 'Project Manager',
            self::ROLE_ENGINEER => 'Engineer',
            self::ROLE_FOREMAN => 'Foreman',
            self::ROLE_ESTIMATOR => 'Estimator',
            self::ROLE_ADMINISTRATOR => 'Administrator'
        ];
    }

    /**
     * @param $role
     * @return mixed|string
     */
    public static function getRoleName($role)
    {
        if(isset(self::getRoles()[$role])){
            return self::getRoles()[$role];
        }

        return '';
    }


    /**
     * @return array
     */
    public static function getCapabilities()
    {
        return [
            self::CAP_EDIT_CLIENT,
            self::CAP_VIEW_CLIENT,
            self::CAP_EDIT_ENQUIRY,
            self::CAP_VIEW_ENQUIRY,
            self::CAP_EDIT_QUOTE,
            self::CAP_VIEW_QUOTE,
            self::CAP_VIEW_PROJECT,
            self::CAP_EDIT_PROJECT,
            self::CAP_EDIT_VARIATION,
            self::CAP_VIEW_VARIATION,
            self::CAP_EDIT_INVOICE,
            self::CAP_VIEW_INVOICE,
            self::CAP_EDIT_TASK,
            self::CAP_VIEW_TASK,
            self::CAP_ALL
        ];
    }
}