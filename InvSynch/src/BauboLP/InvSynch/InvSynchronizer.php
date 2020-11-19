<?php


namespace BauboLP\InvSynch;


use BauboLP\InvSynch\API\MySQLAPI;
use BauboLP\InvSynch\API\SynchAPI;
use BauboLP\InvSynch\Command\InvSynchronizerCommand;
use BauboLP\InvSynch\Events\PlayerJoinEvent;
use BauboLP\InvSynch\Events\PlayerMoveEvent;
use BauboLP\InvSynch\Events\PlayerQuitEvent;

use mysqli;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class InvSynchronizer extends PluginBase
{

    const Prefix = TextFormat::DARK_GRAY . "» " . TextFormat::DARK_PURPLE . TextFormat::BOLD . "VanityMC " . TextFormat::RESET;

    private static $plugin, $connection;

    public static $cantmove = [];

    public function onEnable()
    {
        self::$plugin = $this;
        if (!is_file($this->getDataFolder() . "config.yml")) {
            $c = new Config($this->getDataFolder() . "config.yml", 2);
            $c->set("Host", "Host");
            $c->set("User", "Username");
            $c->set("Password", "Password");
            $c->set("SynchronizeArmor", true);
            $c->save();
            Server::getInstance()->getLogger()->info(TextFormat::RED . "Please update the config.yml to use this plugin! Finish? Restart the Server!");
            $this->setEnabled(FALSE);
        } else {
            if (!MySQLAPI::isDataOverridden()) {
                Server::getInstance()->getLogger()->error(TextFormat::RED . "You must update the config.yml to use this plugin!");
                $this->setEnabled(FALSE);
            } else {
                self::setConnection();
                MySQLAPI::createDataBase();
                MySQLAPI::createTable();
                Server::getInstance()->getCommandMap()->register("InfoCommand", new InvSynchronizerCommand());
                Server::getInstance()->getPluginManager()->registerEvents(new PlayerJoinEvent(), $this);
                Server::getInstance()->getPluginManager()->registerEvents(new PlayerQuitEvent(), $this);
                Server::getInstance()->getPluginManager()->registerEvents(new PlayerMoveEvent(), $this);
                $this->getLogger()->info(TextFormat::GREEN . "loaded!" . TextFormat::AQUA . " Github: https://github.com/Baubo-LP");
            }
        }
    }

    public function onDisable()
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            if (MySQLAPI::isRegistered($player->getName())) {
                InvSynchronizer::getSynchAPI()->setInventoryEncode($player);
                $this->getLogger()->info(TextFormat::DARK_RED . $player->getName() . " gesichert!");
            }
        }
    }

    /**
     * @return InvSynchronizer
     */
    public static function getPlugin(): InvSynchronizer
    {
        return self::$plugin;
    }

    /**
     * @return mysqli
     */
    public static function getConnection(): mysqli
    {
        if(!mysqli_ping(self::$connection) or mysqli_error(self::$connection)) {
            self::setConnection();
        }
        return self::$connection;
    }


    public static function setConnection(): void
    {
        $c = new Config(self::getPlugin()->getDataFolder()."config.yml",2 );
        self::$connection = new mysqli($c->get("Host"), $c->get("User"), $c->get("Password"), "InvSynchronize");
    }

    /**
     * @return SynchAPI
     */
    public static function getSynchAPI(): SynchAPI
    {
        return new SynchAPI();
    }

}