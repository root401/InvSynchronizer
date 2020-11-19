<?php


namespace BauboLP\InvSynch\Events;


use BauboLP\InvSynch\API\MySQLAPI;
use BauboLP\InvSynch\InvSynchronizer;
use BauboLP\InvSynch\Tasks\SynchronItemsDelay;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;

class PlayerJoinEvent implements Listener
{

    public function Join(\pocketmine\event\player\PlayerJoinEvent $event)
    {
        if(!MySQLAPI::isRegistered($event->getPlayer()->getName())) {
            MySQLAPI::registerUser($event->getPlayer()->getName());
            $event->getPlayer()->sendMessage(InvSynchronizer::Prefix.TextFormat::GRAY."You successfully registered!");
        }else {
            InvSynchronizer::$cantmove[] = $event->getPlayer()->getName();
            $effect = Effect::getEffect(Effect::BLINDNESS);
            $event->getPlayer()->addEffect(new EffectInstance($effect, 60, 5, false));
           InvSynchronizer::getPlugin()->getScheduler()->scheduleRepeatingTask(new SynchronItemsDelay($event->getPlayer()), 20);
        }
    }

}