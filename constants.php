<?php

const SECONDS_IN_DAY = 86400; // Используема в  function validateDate() путь  functions/validation.php
const ONE_MB = 1048576;
const LOT_NAME_REQUIRED = true;
const LOT_NAME_MIN_LENGTH = 5;
const LOT_NAME_MAX_LENGTH = 50;
const LOT_CATEGORY_REQUIRED = true;
const LOT_RATE_REQUIRED = true;
const LOT_RATE_MIN_VALUE = 1000;
const LOT_RATE_MAX_VALUE = 100000;
const LOT_STEP_REQUIRED = true;
const LOT_STEP_MIN_VALUE = 100;
const LOT_STEP_MAX_VALUE = 10000;
const LOT_MESSAGE_MIN_LENGTH = 50;
const LOT_MESSAGE_MAX_LENGTH = 5000;
const LOT_MIN_TIME = SECONDS_IN_DAY;
const LOT_MAX_TIME = 5 * SECONDS_IN_DAY;
const LOT_IMG_SIZE = 1 * ONE_MB; //
const LOT_ALLOWED_IMG_EXT = ['jpeg', 'jpg', 'png'];
const IMAGE_PATH = 'uploads';
const LOT_DATE_REQUIRED = true;
const LOT_DATE_FORMAT = 'Y-m-d';
const TOKEN_EXPIRE = 3600;
const REGISTER_PASSWORD_MIN_LENGTH = 12;
const REGISTER_PASSWORD_MAX_LENGTH = 1024;
const REGISTER_NAME_MIN_LENGTH = 6;
const REGISTER_NAME_MAX_LENGTH = 56;
const REGISTER_MESSAGE_MIN_LENGTH = 30;
const REGISTER_MESSAGE_MAX_LENGTH = 500;
const LOTS_PER_PAGE = 9;

const SEARCH_BY_QUERY_STRING = ' AND MATCH(lot_name, lot_desc) AGAINST(?)';
const SEARCH_BY_CATEGORY = ' AND category_name = ? ';

