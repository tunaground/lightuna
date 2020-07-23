<?php
namespace Lightuna\Object;

/**
 * Class Board
 * @package Lightuna\Object
 */
class Board extends \ArrayObject
{
    /**
     * Board constructor.
     * @param array $config
     * @param string $boardUid
     * @throws \UnexpectedValueException
     */
    public function __construct(array $config, string $boardUid)
    {
        if (!isset($config['boards'][$boardUid])) {
            throw new \UnexpectedValueException(MSG_INVALID_BOARD_UID);
        }
        parent::__construct(
            array_merge(
                ['id' => $boardUid],
                $config['boards']['__default__'],
                $config['boards'][$boardUid]
            )
        );

    }
}
