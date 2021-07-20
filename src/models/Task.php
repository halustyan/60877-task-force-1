<?php
declare(strict_types=1);

namespace Htmlacademy\Models;


use Htmlacademy\Models\ActionCancel;
use Htmlacademy\Models\ActionDeny;
use Htmlacademy\Models\ActionDone;
use Htmlacademy\Models\AbstractClass;
use Htmlacademy\Models\ActionExecute;
use Htmlacademy\Exceptions\CustomException;

error_reporting(E_ALL);

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

    private $executerId;
    private $customerId;
    private $status = self::STATUS_NEW;


    private $statusArray = [
		self::STATUS_NEW => 'Новое',
		self::STATUS_INWORK => 'В работе',
		self::STATUS_DONE => 'Выполнено',
		self::STATUS_FAILED => 'Провалено',
		self::STATUS_CANCELLED => 'Отменено'
	];
    function showConstant() {
        echo  self::STATUS_INWORK . "\n";
    }
    private $statusMap = [
		self::ACTION_EXECUTE => self::STATUS_INWORK,
		self::ACTION_DONE => self::STATUS_DONE,
		self::ACTION_CANCEL => self::STATUS_CANCELLED,
		self::ACTION_DENY => self::STATUS_FAILED
	];

    public function __construct(int $customerId, int $executerId)
    {
        $this->customerId = $customerId;
        $this->executerId = $executerId;
    }

    public function actionArray() {
        $array = [
            self::STATUS_NEW => [new ActionExecute(), new ActionCancel()],
            self::STATUS_INWORK => [new ActionDone(), new ActionDeny()],
        ];
        return $array;
    }
     function getActions(string $status, int $idExecutor, int $idTaskmaker, int $idUser)
    {


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

    public function nextStatus(string $action):string
    {
        if(!in_array($action, array_keys( $this->statusMap ))){
			throw new CustomException("Given action does not exist.");
		}
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

