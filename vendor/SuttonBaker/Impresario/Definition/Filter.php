<?php

namespace SuttonBaker\Impresario\Definition;

class Filter
{
    const COMPARE_TYPE = 'compare_type';
    const MAP = 'map';
    const FIELD_TYPE = 'field_type';
    const DATA_CONVERTER = 'data_converter';

    const DATA_CONVERTER_CLASS = 'class';
    const DATA_CONVERTER_METHOD = 'method';

    const COMPARE_TYPE_EQ = '=';
    const COMPARE_TYPE_LIKE = 'like';
    const COMPARE_TYPE_GT = '>';
    const COMPARE_TYPE_LT = '<';
    const COMPARE_TYPE_RANGE = 'range';
    
    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_RANGE = 'range';

    const RANGE_LOW = 'low';
    const RANGE_HIGH = 'high';
}