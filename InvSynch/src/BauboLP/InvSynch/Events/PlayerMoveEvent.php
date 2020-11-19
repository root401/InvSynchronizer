<?php


namespace BauboLP\InvSynch\Events;


use BauboLP\InvSynch\InvSynchronizer;
use pocketmine\event\Listener;

class PlayerMoveEvent implements Listener
{

    public function Move(\pocketmine\event\player\PlayerMoveEvent $event)
    {
        if(in_array($event->getPlayer()->getName(), InvSynchronizer::$cantmove)) {
            $event->getPlayer()->setImmobile();
        }else {
            $event->getPlayer()->setImmobile(false);
        }
    }

}