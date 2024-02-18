<?php

declare(strict_types=1);

namespace CommonPHP\Web\Support;

/**
 * Class ResponseStatus
 *
 * This class represents the various HTTP response status codes.
 * It is an enumeration with each value representing a specific status code.
 *
 * @package CommonPHP\Web\Support
 */
enum ResponseStatus: int
{
    case INFO_CONTINUE = 100;
    case INFO_SWITCHING_PROTOCOLS = 101;
    case INFO_PROCESSING = 102;
    case INFO_EARLY_HINTS = 103;
    
    case SUCCESS_OK = 200;
    case SUCCESS_CREATED = 201;
    case SUCCESS_ACCEPTED = 202;
    case SUCCESS_NON_AUTHORITATIVE_INFORMATION = 203;
    case SUCCESS_NO_CONTENT = 204;
    case SUCCESS_RESET_CONTENT = 205;
    case SUCCESS_PARTIAL_CONTENT = 206;
    case SUCCESS_MULTI_STATUS = 207;
    case SUCCESS_ALREADY_REPORTED = 208;
    case SUCCESS_IM_USED = 226;
    
    case REDIRECT_MULTIPLE_CHOICES = 300;
    case REDIRECT_MOVED_PERMANENTLY = 301;
    case REDIRECT_FOUND = 302;
    case REDIRECT_SEE_OTHER = 303;
    case REDIRECT_NOT_MODIFIED = 304;
    case REDIRECT_USE_PROXY = 305;
    case REDIRECT_TEMPORARY_REDIRECT = 307;
    case REDIRECT_PERMANENT_REDIRECT = 308;
    
    case CLIENT_ERROR_BAD_REQUEST = 400;
    case CLIENT_ERROR_UNAUTHORIZED = 401;
    case CLIENT_ERROR_PAYMENT_REQUIRED = 402;
    case CLIENT_ERROR_FORBIDDEN = 403;
    case CLIENT_ERROR_NOT_FOUND = 404;
    case CLIENT_ERROR_METHOD_NOT_ALLOWED = 405;
    case CLIENT_ERROR_NOT_ACCEPTABLE = 406;
    case CLIENT_ERROR_PROXY_AUTHENTICATION_REQUIRED = 407;
    case CLIENT_ERROR_REQUEST_TIMEOUT = 408;
    case CLIENT_ERROR_CONFLICT = 409;
    case CLIENT_ERROR_GONE = 410;
    case CLIENT_ERROR_LENGTH_REQUIRED = 411;
    case CLIENT_ERROR_PRECONDITION_FAILED = 412;
    case CLIENT_ERROR_PAYLOAD_TOO_LARGE = 413;
    case CLIENT_ERROR_URI_TOO_LONG = 414;
    case CLIENT_ERROR_UNSUPPORTED_MEDIA_TYPE = 415;
    case CLIENT_ERROR_RANGE_NOT_SATISFIABLE = 416;
    case CLIENT_ERROR_EXPECTATION_FAILED = 417;
    case CLIENT_ERROR_IM_A_TEAPOT = 418;
    case CLIENT_ERROR_MISDIRECTED_REQUEST = 421;
    case CLIENT_ERROR_UNPROCESSABLE_CONTENT = 422;
    case CLIENT_ERROR_LOCKED = 423;
    case CLIENT_ERROR_FAILED_DEPENDENCY = 424;
    case CLIENT_ERROR_TOO_EARLY = 425;
    case CLIENT_ERROR_UPGRADE_REQUIRED = 426;
    case CLIENT_ERROR_PRECONDITION_REQUIRED = 428;
    case CLIENT_ERROR_TOO_MANY_REQUESTS = 429;
    case CLIENT_ERROR_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    case CLIENT_ERROR_UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    case SERVER_ERROR_INTERNAL_SERVER_ERROR = 500;
    case SERVER_ERROR_NOT_IMPLEMENTED = 501;
    case SERVER_ERROR_BAD_GATEWAY = 502;
    case SERVER_ERROR_SERVICE_UNAVAILABLE = 503;
    case SERVER_ERROR_GATEWAY_TIMEOUT = 504;
    case SERVER_ERROR_HTTP_VERSION_NOT_SUPPORTED = 505;
    case SERVER_ERROR_VARIANT_ALSO_NEGOTIATES = 506;
    case SERVER_ERROR_INSUFFICIENT_STORAGE = 507;
    case SERVER_ERROR_LOOP_DETECTED = 508;
    case SERVER_ERROR_NOT_EXTENDED = 510;
    case SERVER_ERROR_NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * Retrieves the message associated with the current object.
     *
     * @return string The message associated with the current object.
     */
    public function getMessage(): string
    {
        return match ($this) {
             ResponseStatus::INFO_CONTINUE => "Continue",
             ResponseStatus::INFO_SWITCHING_PROTOCOLS => "Switching Protocols",
             ResponseStatus::INFO_PROCESSING => "Processing",
             ResponseStatus::INFO_EARLY_HINTS => "Early Hints",
            
             ResponseStatus::SUCCESS_OK => "OK",
             ResponseStatus::SUCCESS_CREATED => "Created",
             ResponseStatus::SUCCESS_ACCEPTED => "Accepted",
             ResponseStatus::SUCCESS_NON_AUTHORITATIVE_INFORMATION => "Non-Authoritative Information",
             ResponseStatus::SUCCESS_NO_CONTENT => "No Content",
             ResponseStatus::SUCCESS_RESET_CONTENT => "Reset Content",
             ResponseStatus::SUCCESS_PARTIAL_CONTENT => "Partial Content",
             ResponseStatus::SUCCESS_MULTI_STATUS => "Multi-Status",
             ResponseStatus::SUCCESS_ALREADY_REPORTED => "Already Reported",
             ResponseStatus::SUCCESS_IM_USED => "IM Used",
            
             ResponseStatus::REDIRECT_MULTIPLE_CHOICES => "Multiple Choices",
             ResponseStatus::REDIRECT_MOVED_PERMANENTLY => "Moved Permanently",
             ResponseStatus::REDIRECT_FOUND => "Found",
             ResponseStatus::REDIRECT_SEE_OTHER => "See Other",
             ResponseStatus::REDIRECT_NOT_MODIFIED => "Not Modified",
             ResponseStatus::REDIRECT_USE_PROXY => "Use Proxy",
             ResponseStatus::REDIRECT_TEMPORARY_REDIRECT => "Temporary Redirect",
             ResponseStatus::REDIRECT_PERMANENT_REDIRECT => "Permanent Redirect",
            
             ResponseStatus::CLIENT_ERROR_BAD_REQUEST => "Bad Request",
             ResponseStatus::CLIENT_ERROR_UNAUTHORIZED => "Unauthorized",
             ResponseStatus::CLIENT_ERROR_PAYMENT_REQUIRED => "Payment Required",
             ResponseStatus::CLIENT_ERROR_FORBIDDEN => "Forbidden",
             ResponseStatus::CLIENT_ERROR_NOT_FOUND => "Not Found",
             ResponseStatus::CLIENT_ERROR_METHOD_NOT_ALLOWED => "Method Not Allowed",
             ResponseStatus::CLIENT_ERROR_NOT_ACCEPTABLE => "Not Acceptable",
             ResponseStatus::CLIENT_ERROR_PROXY_AUTHENTICATION_REQUIRED => "Proxy-Authentication Required",
             ResponseStatus::CLIENT_ERROR_REQUEST_TIMEOUT => "Request Timeout",
             ResponseStatus::CLIENT_ERROR_CONFLICT => "Conflict",
             ResponseStatus::CLIENT_ERROR_GONE => "Gone",
             ResponseStatus::CLIENT_ERROR_LENGTH_REQUIRED => "Length Required",
             ResponseStatus::CLIENT_ERROR_PRECONDITION_FAILED => "Precondition Failed",
             ResponseStatus::CLIENT_ERROR_PAYLOAD_TOO_LARGE => "Payload Too Large",
             ResponseStatus::CLIENT_ERROR_URI_TOO_LONG => "URI Too Long",
             ResponseStatus::CLIENT_ERROR_UNSUPPORTED_MEDIA_TYPE => "Unsupported Media Type",
             ResponseStatus::CLIENT_ERROR_RANGE_NOT_SATISFIABLE => "Range Not Satisfiable",
             ResponseStatus::CLIENT_ERROR_EXPECTATION_FAILED => "Expectation Failed",
             ResponseStatus::CLIENT_ERROR_IM_A_TEAPOT => "I'm a teapot",
             ResponseStatus::CLIENT_ERROR_MISDIRECTED_REQUEST => "Misdirected Request",
             ResponseStatus::CLIENT_ERROR_UNPROCESSABLE_CONTENT => "Unprocessable Content",
             ResponseStatus::CLIENT_ERROR_LOCKED => "Locked",
             ResponseStatus::CLIENT_ERROR_FAILED_DEPENDENCY => "Failed Dependency",
             ResponseStatus::CLIENT_ERROR_TOO_EARLY => "Too Early",
             ResponseStatus::CLIENT_ERROR_UPGRADE_REQUIRED => "Upgrade Required",
             ResponseStatus::CLIENT_ERROR_PRECONDITION_REQUIRED => "Precondition Required",
             ResponseStatus::CLIENT_ERROR_TOO_MANY_REQUESTS => "Too Many Requests",
             ResponseStatus::CLIENT_ERROR_REQUEST_HEADER_FIELDS_TOO_LARGE => "Request Header Fields Too Large",
             ResponseStatus::CLIENT_ERROR_UNAVAILABLE_FOR_LEGAL_REASONS => "Unavailable for Legal Reasons",
        
             ResponseStatus::SERVER_ERROR_INTERNAL_SERVER_ERROR => "Internal Server Error",
             ResponseStatus::SERVER_ERROR_NOT_IMPLEMENTED => "Not Implemented",
             ResponseStatus::SERVER_ERROR_BAD_GATEWAY => "Bad Gateway",
             ResponseStatus::SERVER_ERROR_SERVICE_UNAVAILABLE => "Service Unavailable",
             ResponseStatus::SERVER_ERROR_GATEWAY_TIMEOUT => "Gateway Timeout",
             ResponseStatus::SERVER_ERROR_HTTP_VERSION_NOT_SUPPORTED => "HTTP Version Not Supported",
             ResponseStatus::SERVER_ERROR_VARIANT_ALSO_NEGOTIATES => "Variant Also Negotiates",
             ResponseStatus::SERVER_ERROR_INSUFFICIENT_STORAGE => "Insufficient Storage",
             ResponseStatus::SERVER_ERROR_LOOP_DETECTED => "Loop Detected",
             ResponseStatus::SERVER_ERROR_NOT_EXTENDED => "Not Extended",
             ResponseStatus::SERVER_ERROR_NETWORK_AUTHENTICATION_REQUIRED => "Network Authentication Required",
        };
    }
}