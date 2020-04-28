<?php
namespace Lightuna\Exception;

/**
 * Class ExceptionMessage
 * @package Lightuna\Exception
 */
class ExceptionMessage
{
    const CONNECT_FAILED = '데이터베이스 연결 실패';
    const TRANSACTION_FAILED = '데이터베이스 트랜잭션 처리 실패';
    const QUERY_FAILED = '데이터베이스 질의 실패';
}