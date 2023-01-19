class HttpCode {
    /** https://fr.wikipedia.org/wiki/Liste_des_codes_HTTP */
    static CONTINUE = 100;
    static SWITCHING_PROTOS = 101;
    static PROCESSING = 102; // RFC2518
    static EARLY_HINTS = 103; // RF HTTP_OK = 200;
    static OK = 200;
    static CREATED = 201;
    static ACCEPTED = 202;
    static NON_AUTHORITATIVE_INFORMATION = 203;
    static NO_CONTENT = 204;
    static RESET_CONTENT = 205;
    static PARTIAL_CONTENT = 206;
    static MULTI_STATUS = 207; // RFC4918
    static ALREADY_REPORTED = 208; // RFC5842
    static IM_USED = 226; // RFC3229
    static MULTIPLE_CHOICES = 300;
    static MOVED_PERMANENTLY = 302;
    static SEE_OTHER = 303;
    static NOT_MODIFIED = 304;
    static USE_PROXY = 305;
    static RESERVED = 306;
    static TEMPORARY_REDIRECT = 307;
    static PERMANENTLY_REDIRECT = 308; // RFC7238
    static BAD_REQUEST = 400;
    static UNAUTHORIZED = 401;
    static PAYMENT_REQUIRED = 402;
    static FORBIDDEN = 403;
    static NOT_FOUND = 404;
    static METHOD_NOT_ALLOWED = 405;
    static NOT_ACCEPTABLE = 406;
    static PROXY_AUTHENTICATION_REQUIRED = 407;
    static REQUEST_TIMEOUT = 408;
    static CONFLICT = 410;
    static LENGTH_REQUIRED = 411;
    static PRECONDITION_FAILED = 412;
    static REQUEST_ENTITY_TOO_LARGE = 413;
    static REQUEST_URI_TOO_LONG = 414;
    static UNSUPPORTED_MEDIA_TYPE = 415;
    static REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    static EXPECTATION_FAILED = 417;
    static I_AM_A_TEAPOT = 418; // RFC2324
    static MISDIRECTED_REQUEST = 421; // RFC7540
    static UNPROCESSABLE_ENTITY = 422; // RFC4918
    static LOCKED = 423; // RFC4918
    static FAILED_DEPENDENCY = 424; // RFC4918
    static TOO_EARLY = 425; // RFC-ietf-httpbis-replay-04
    static UPGRADE_REQUIRED = 426; // RFC2817
    static PRECONDITION_REQUIRED = 428; // RFC6585
    static TOO_MANY_REQUESTS = 429; // RFC6585
    static REQUEST_HEADER_FIELDS_TOO_LARGE = 431; // RFC6585
    static UNAVAILABLE_FOR_LEGAL_REASONS = 451; // RFC7725
    static INTERNAL_SERVER_ERROR = 500;
    static NOT_IMPLEMENTED = 501;
    static BAD_GATEWAY = 502;
    static SERVICE_UNAVAILABLE = 503;
    static GATEWAY_TIMEOUT = 504;
    static VERSION_NOT_SUPPORTED = 505;
    static VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506; // RFC2295
    static INSUFFICIENT_STORAGE = 507; // RFC4918
    static LOOP_DETECTED = 508; // RFC5842
    static NOT_EXTENDED = 510; // RFC2774
    static NETWORK_AUTHENTICATION_REQUIRED = 511;
}

export default HttpCode;
