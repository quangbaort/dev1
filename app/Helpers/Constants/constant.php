<?php

if (!defined('DEFINE_CONSTANT')) {
    define('DEFINE_CONSTANT', 'DEFINE_CONSTANT');

    define('PAGE_SIZE', 20);
    define('FORM_INPUT_MAX_LENGTH', 255);

    /** List roles of application */
    define('SUPER_ADMIN_ROLE', 0);    // Supper Admin - No identifier needed in table user_roles
    define('ADMIN_ROLE', 1);          // Admin of Organization
    define('USER_ROLE', 5);           // Normal user

    define('ADMIN', 'ADMIN');
    define('NORMAL_USER', 'NORMAL_USER');

    /** List repeat of safety */
    define('NO_REPEAT', 0);           // No repeat
    define('REPEAT_EVERYDAY', 1);     // repeat everyday
    define('REPEAT_WEEK', 2);         // Repeat  day of week
    define('REPEAT_DATE', 3);         // Repeat day of month

    define('SAFETY_NO_ANSWER', 0); // No answer safety
    define('SAFETY_ANSWER_SAFE', 1); // OK
    define('SAFETY_ANSWER_NG', 2); // Support required (NG)
    define('SAFETY_UNDER_SUPPORT', 1);
    define('SAFETY_SUPPORTED', 2); // Support required (NG)

    /** Setup day repeat of safety */
    define('SET_DAY_REPEAT_EVERYDAY', 6);     // No repeat
    define('SET_DAY_REPEAT_WEEK', 30);        // Repeat  day of week
    define('SET_DAY_REPEAT_DATE', 364);       // Repeat day of month

    /** List status read of notify */
    define('READ', 1);                //already read
    define('UNREAD', 0);              //unread

    /**
     * Event
     */
    define('EVENT_ANSWER_JOIN', 1);
    define('EVENT_ANSWER_HOLD', 2);
    define('EVENT_ANSWER_REJECT', 3);

    /** Log action type */
    define('CREATE_LOG_TYPE', 1);
    define('EDIT_LOG_TYPE', 2);
    define('DELETE_LOG_TYPE', 3);

    /** Public safe repeat sending time  */
    define('SAFETY_CHECK_MAX_DURATION', 8);

    /**
     * File Management
     * file type: type of function for management files
     */
    define('FILE_TYPE_MEMO', 'memo');
    define('FILE_TYPE_NEWS', 'news');
    define('FILE_TYPE_USER', 'user');
    define('FILE_TYPE_EVENT', 'event');
    define('FILE_TYPE_MEETING', 'meeting');
    define('FILE_TYPE_FOLDER', 'folder');


    /** String to hash id before send mail to user */
    define('HASH_STRING', 'hash to send mail');

    /** List actions account limit of organization */
    define('MINUS_ONE_ACCOUNT', 1); //Minus account limit of organization
    define('PLUS_ONE_ACCOUNT', 1);  //Plus account limit of organization
}
