<?php


namespace BauboLP\InvSynch\Command;


use BauboLP\InvSynch\InvSynchronizer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class InvSynchronizerCommand extends Command
{

    public function __construct()
    {
        parent::__construct('invsychronizer', 'made by BauboLP', '', ['invsynch']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $sender->sendMessage(InvSynchronizer::Prefix.TextFormat::AQUA."Plugin created by BauboLP. Github: https://github.com/Baubo-LP");
    }

}