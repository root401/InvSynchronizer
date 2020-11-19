<?php


namespace BauboLP\InvSynch\API;


use BauboLP\InvSynch\InvSynchronizer;
use pocketmine\level\sound\BlazeShootSound;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class SynchAPI
{

    public function setInventoryEncode(Player $player): void
    {
        $inventory = [];
        $armor_inventory = [];

        foreach ($player->getInventory()->getContents() as $slot => $item) {
            $inventory[$slot] = $item;
        }

        foreach ($player->getArmorInventory()->getContents() as $slot => $armor) {
            $armor_inventory[$slot] = $armor;
        }

        $encoded_inv = base64_encode(serialize($inventory));
        $encoded_armor = base64_encode(serialize($armor_inventory));

        MySQLAPI::updateInventorys($player->getName(), $encoded_inv, $encoded_armor);
    }

    public function synchronizeInventory(Player $player)
    {
       $inventory = MySQLAPI::getInventory($player->getName());
       $armor_inventory = MySQLAPI::getArmorInventory($player->getName());

       $player->getArmorInventory()->clearAll();
       $player->getInventory()->clearAll();

       foreach ($inventory as $slot => $item) {
           $player->getInventory()->setItem($slot, $item);
       }
        $c = new Config(InvSynchronizer::getPlugin()->getDataFolder()."config.yml",2 );
       if($c->get("SynchronizeArmor") == true) {
           foreach ($armor_inventory as $slot => $armor) {
               $player->getArmorInventory()->setItem($slot, $armor);
           }
       }

       $player->getLevel()->addSound(new BlazeShootSound($player));
       $player->sendMessage(InvSynchronizer::Prefix.TextFormat::GREEN."Daten erfolgreich geladen!");
    }
}