<?php
// Here you can initialize variables that will for your tests

date_default_timezone_set('EET');

if (! defined('HTTP_RESPONSE_OK')) {
    define('HTTP_RESPONSE_OK', 200);
    define('HTTP_RESPONSE_CREATED', 201);
    define('HTTP_RESPONSE_NOT_MODIFIED', 304);
    define('HTTP_RESPONSE_BAD_REQUEST', 400);
    define('HTTP_RESPONSE_UNAUTHORIZED', 401);
    define('HTTP_RESPONSE_FORBIDDEN', 403);
    define('HTTP_RESPONSE_NOT_FOUND', 404);
    define('HTTP_RESPONSE_CONFLICT', 409);
    define('HTTP_RESPONSE_PRECONDITION_FAILED', 412);
    define('HTTP_RESPONSE_INTERNAL_SERVICE_ERROR', 500);
    define('HTTP_RESPONSE_SERVICE_UNAVAILABLE', 503);
}

defined('FACEBOOK_USER_EMAIL') || define('FACEBOOK_USER_EMAIL', 'samva.debug@gmail.com');

// token will expire at 1366457603 (on "2013-04-20 14:33:23")
if (! defined('FACEBOOK_USER_TOKEN_VALID')) {
    define(
        'FACEBOOK_USER_TOKEN_VALID',
        'BAABxIy7ib8YBAJP8s8O0NpOKDLW0Pc4vckvyOnzHTZBBOOAPKAMTIdVs6zpvNVy4B8ZA0Yt8jPHRWJXesSPd1pPVSEGSRCC02UaRk541RVQTUnB34yEZA0AvJXbZCb41sQJffSLpB4PZAPrw5ZBFc0'
        // 'AAAGcjfA6N6IBAL1Lh03UZANyLNEvcu3jHE5fi3zK9PdD2QMetJL0OikLyVnMKF6QeKzXuDLWujXx4mX3aDWwu6GIu4NZCZBCuP8BY40owZDZD'
        // 'AAAGcjfA6N6IBAMj5k1e1B40jAl8tPCqSwOw2ZCZB689mdOqfNifZCO8Y97jSR9xC0XWSbz5z1Wqmt5qn9eaWlTvVLZCnZArB2A2M3QHnaYnNZBcj0vU5sA'
    );
}

// token expired at 1354885200 (on "2012-12-07 14:00:00")
if (! defined('FACEBOOK_USER_TOKEN_EXPIRED')) {
    define(
        'FACEBOOK_USER_TOKEN_EXPIRED',
        'AAACEdEose0cBAH2ZAluOXE9g0fAcF6WZBW8ZA243uDphcpGE9QctaCpk8yQ'
        .'29TBri0VkO7OIYOa10AEEDaZAM3XEh055UP9xhR9mMbKbtmX4i6HIcKOW'
    );
}

if (! defined('FACEBOOK_USER_TOKEN_INVALID')) {
    define(
        'FACEBOOK_USER_TOKEN_INVALID',
        'WRONGTESTTOKENJ7cCXnksYsNAhH5YCddaMzU36ZBU3IZBTGBUKz80DIY9sg'
        .'K3YWFFeRLECPZC7p2fboOy6EgagGRqY0E5w8XSX7SdxJBvKEMh5OGmZCY'
    );
}

defined('TEST_IMAGE_FILE_LABEL') || define('TEST_IMAGE_FILE_LABEL', 'Test name');
defined('TEST_IMAGE_FILE_PATH')  || define('TEST_IMAGE_FILE_PATH', realpath(__DIR__ . '/..') . '/_data/smile2.jpg');

defined('SOLUTION_ADMIN_EMAIL') || define('SOLUTION_ADMIN_EMAIL', 'bo.test.solution.admin@nmotion.pp.ciklum.com');
defined('SOLUTION_ADMIN_PASSWORD') || define('SOLUTION_ADMIN_PASSWORD', 'qwertyui');

if (!defined('ORDER_STATUS_NEW_ORDER')) {
    define('ORDER_STATUS_NEW_ORDER', 1);
    define('ORDER_STATUS_PENDING_PAYMENT', 2);
    define('ORDER_STATUS_PAID', 3);
    define('ORDER_STATUS_FAILED', 4);
    define('ORDER_STATUS_CANCELLED', 5);
    define('ORDER_STATUS_SENT_TO_PRINTER', 6);
}

if (!defined('ORDER_EXCEPTION_PAYING_BY_OTHER')) {
    define('ORDER_EXCEPTION_PAYING_BY_OTHER', 1005001);
    define('ORDER_EXCEPTION_PAID_BY_OTHER', 1005002);

    define('EXCEPTION_CODE_TABLE_MAYBE_EMPTY', 1002);
}

if (!defined('MEAL_NOT_AVAILABLE_MEAL_NOT_VISIBLE')) {
    define('MEAL_NOT_AVAILABLE_MEAL_NOT_VISIBLE', 10050021);
    define('MEAL_NOT_AVAILABLE_CATEGORY_NOT_VISIBLE', 10050022);
    define('MEAL_NOT_AVAILABLE_MEAL_TIME_UNAVAILABLE', 10050023);
    define('MEAL_NOT_AVAILABLE_CATEGORY_TIME_UNAVAILABLE', 10050024);
}

if (!defined('RESTAURANT_SERVICE_TYPE_IN_HOUSE')) {
    define('RESTAURANT_SERVICE_TYPE_IN_HOUSE', 1);
    define('RESTAURANT_SERVICE_TYPE_TAKEAWAY', 2);
    define('RESTAURANT_SERVICE_TYPE_ROOM_SERVICE', 3);
}
