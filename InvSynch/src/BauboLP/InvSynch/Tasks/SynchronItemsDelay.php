<?php


namespace BauboLP\InvSynch\Tasks;


use BauboLP\InvSynch\InvSynchronizer;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class SynchronItemsDelay extends Task
{

    private $player;
    private $i = 0;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function onRun(int $currentTick)
    {
       if($this->player) {
           $this->player->getInventory()->clearAll();
           $this->player->getArmorInventory()->clearAll();
           $this->i++;
           if($this->i === 1) {
               $this->player->addTitle(TextFormat::RED."Load Inventory".TextFormat::GREEN.".");
               return;
           }else if($this->i === 2) {
               $this->player->addTitle(TextFormat::RED."Load Inventory".TextFormat::GREEN."..");
               return;
           }else if($this->i === 3) {
               $this->player->addTitle(TextFormat::RED."Load Inventory".TextFormat::GREEN."...");
               return;
           }
           InvSynchronizer::getSynchAPI()->synchronizeInventory($this->player);
           unset(InvSynchronizer::$cantmove[array_search($this->player->getName(), InvSynchronizer::$cantmove)]);
           InvSynchronizer::getPlugin()->getScheduler()->cancelTask($this->getTaskId());
       }
    }

}