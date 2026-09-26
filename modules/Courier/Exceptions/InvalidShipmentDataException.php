<?php

namespace Modules\Courier\Exceptions;

/**
 * Raised when an order cannot be booked because its recipient data violates
 * the courier's documented field constraints. This is a permanent data
 * problem: the booking job must record it without retrying.
 */
class InvalidShipmentDataException extends \RuntimeException {}
