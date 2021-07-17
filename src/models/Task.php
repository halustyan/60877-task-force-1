<?php
declare(strict_types=1);
namespace Htmlacademy\Models;

use Htmlacademy\Models\ActionCancel;
use Htmlacademy\Models\ActionDeny;
use Htmlacademy\Models\ActionDone;
use Htmlacademy\Models\AbstractClass;

class Task
{
    const STATUS_NEW = "new";
    const STATUS_INWORK = "in_progress";
    const STATUS_DONE = "done";
    const STATUS_FAILED = "failed";
    const STATUS_CANCELLED = "canceled";

    const ACTION_EXECUTE = "execute";
    const ACTION_DONE = "done";
    const ACTION_CANCEL = "cancel";
    const ACTION_DENY = "deny";

    public function actionArray() {
        $array = [
            self::STATUS_NEW => [new ActionExecute(), new ActionCancel()],
            self::STATUS_EXECUTE => [new ActionDone(), new ActionDeny()],
        ];
        return $array;
    }

    private $executerId;
    private $customerId;
    private $status = self::STATUS_NEW;

    public function __construct(int $customerId, int $executerId)
    {
        $this->customerId = $customerId;
        $this->executerId = $executerId;
    }

    private $statusArray = [
		self::STATUS_NEW => 'Новое',
		self::STATUS_EXECUTE => 'В работе',
		self::STATUS_DONE => 'Выполнено',
		self::STATUS_FAIL => 'Провалено',
		self::STATUS_CANCEL => 'Отменено'
	];

    private $statusMap = [
		self::ACTION_EXECUTE => self::STATUS_EXECUTE,
		self::ACTION_DONE => self::STATUS_DONE,
		self::ACTION_CANCEL => self::STATUS_CANCEL,
		self::ACTION_DENY => self::STATUS_FAIL
	];

     function getActions(string $status, int $idExecutor, int $idTaskmaker, int $idUser)
    {
        $array = [
            self::STATUS_NEW => [new ActionExecute(), new ActionCancel()],
            self::STATUS_EXECUTE => [new ActionDone(), new ActionDeny()],
        ];

        $statuses  = $this->actionArray();

        if (!array_key_exists($status, $statuses)){
            throw CustomExeption ("No status in the action");
        }
        $actions = $statuses[$status];


        foreach ($actions as $action) {
            if ($action->CheckRights($idExecutor, $idTaskmaker, $idUser)) {
                return $action;
            }
        }

        return false;
    }

    public function nextStatus(string $action):int
    {
        $stmap = $this->statusMap[$action];
        return $this->statusArray[$stmap];
    }

    public function getStatus():string
    {
        return $this->status;
    }

    public function getCustomer():int
    {
        return $this->customerId;
    }

    public function getExecuter():int
    {
        return $this->executerId;
    }
}
