<?php


namespace BauboLP\InvSynch\Events;


use BauboLP\InvSynch\API\MySQLAPI;
use BauboLP\InvSynch\InvSynchronizer;
use pocketmine\event\Listener;

class PlayerQuitEvent implements Listener
{

    public function Quit(\pocketmine\event\player\PlayerQuitEvent $event)
    {
        if(MySQLAPI::isRegistered($event->getPlayer()->getName())) {
            InvSynchronizer::getSynchAPI()->setInventoryEncode($event->getPlayer());
        }
    }

}