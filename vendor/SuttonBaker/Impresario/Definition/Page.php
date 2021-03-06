<?php

namespace SuttonBaker\Impresario\Definition;
/**
 * Class Page
 * @package SuttonBaker\Impresario\Definition
 *
 * Contains a list of layout handles for each defined page
 */
class Page
{
    const CLIENT_LIST = 'client_list';
    const CLIENT_EDIT = 'client_edit';
    
    const SUPPLIER_LIST = 'supplier_list';
    const SUPPLIER_EDIT = 'supplier_edit';

    const ENQUIRY_LIST = 'enquiry_list';
    const ENQUIRY_EDIT = 'enquiry_edit';
    const ENQUIRY_REPORT_DOWNLOAD = 'enquiry_report_download';

    const TASK_LIST = 'task_list';
    const TASK_EDIT = 'task_edit';

    const QUOTE_LIST = 'quote_list';
    const QUOTE_EDIT = 'quote_edit';
    const QUOTE_REPORT_DOWNLOAD = 'quote_report_download';

    const PROJECT_LIST = 'project_list';
    const PROJECT_EDIT = 'project_edit';
    const PROJECT_REPORT_DOWNLOAD = 'project_report_download';

    const VARIATION_EDIT = 'variation_edit';
    const INVOICE_EDIT = 'invoice_edit';
    const COST_EDIT = 'cost_edit';

    const ARCHIVE_LIST = 'archive_list';
    const ARCHIVE_REPORT_DOWNLOAD = 'archive_report_download';
}