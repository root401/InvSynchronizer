<?php


namespace BauboLP\InvSynch\API;


use BauboLP\InvSynch\InvSynchronizer;
use pocketmine\utils\Config;

class MySQLAPI
{

    public static function createDataBase(): void
    {
        InvSynchronizer::getConnection()->query("CREATE DATABASE IF NOT EXISTS InvSynchronize");
    }

    public static function createTable(): void
    {
        InvSynchronizer::getConnection()->query("CREATE TABLE IF NOT EXISTS Inventorys(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(64) NOT NULL, inv TEXT, armor TEXT)");
    }

    public static function isDataOverridden(): bool
    {
        $c = new Config(InvSynchronizer::getPlugin()->getDataFolder()."config.yml", 2);
        if($c->get("Password") == "Password") {
            return false;
        }
        return true;
    }

    public static function registerUser(string $username): void
    {
            InvSynchronizer::getConnection()->query("INSERT INTO `Inventorys`(`playername`, `inv`) VALUES ('$username', '')");
    }

    public static function isRegistered(string $username): bool
    {
        $result = InvSynchronizer::getConnection()->query("SELECT * FROM `Inventorys` WHERE playername='$username'");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                return true;
            }
        }
        return false;
    }

    public static function updateInventorys(string $username, string $inventory, string $armorinventory)
    {
        InvSynchronizer::getConnection()->query("UPDATE Inventorys SET inv='$inventory',armor='$armorinventory' WHERE playername='$username'");
    }


    public static function getArmorInventory(string $username)
    {
        $c = new Config(InvSynchronizer::getPlugin()->getDataFolder() . "config.yml", 2);
        if ($c->get("SynchronizeArmor") == true) {
            $result = InvSynchronizer::getConnection()->query("SELECT armor FROM `Inventorys` WHERE playername='$username'");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $armor_inv_string = base64_decode($row['armor']);
                    $armor_inv = unserialize($armor_inv_string);
                    return $armor_inv;
                }
            }
        }
        return null;
    }

    public static function getInventory(string $username)
    {
        $result = InvSynchronizer::getConnection()->query("SELECT inv FROM `Inventorys` WHERE playername='$username'");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $inv_string = base64_decode($row['inv']);
                $inv = unserialize($inv_string);
                return $inv;
            }
        }
        return null;
    }




}