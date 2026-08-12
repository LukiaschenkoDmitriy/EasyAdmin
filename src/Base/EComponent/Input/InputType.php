<?php declare(strict_types=1);

namespace EAdmin\Core\Base\EComponent\Input;

enum InputType: string {
    case CHECKBOX = "checkbox";
    case COLOR = "color";
    case DATE = "date";
    case DATETIME_LOCAL = "datetime-local";
    case EMAIL = "email";
    case FILE = "file";
    case HIDDEN = "hidden";
    case MONTH = "month";
    case NUMBER = "number";
    case PASSWORD = "password";
    case RANGE = "range";
    case TEL = "tel";
    case TEXT = "text";
    case URL = "url";
}