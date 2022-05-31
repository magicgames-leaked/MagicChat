<?php

namespace Pushkar\MagicChat;

use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerChatEvent;

class Main extends PluginBase implements Listener
{
    private array $defaultWords = [];

    public function onEnable(): void
    {
        $this->saveResource("config.yml");
        $this->saveResource("profanity_filter.wlist");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->defaultWords = file($this->getDataFolder() . "profanity_filter.wlist", FILE_IGNORE_NEW_LINES);

        $cmdMap = $this->getServer()->getCommandMap();
        $pmmpme = $cmdMap->getCommand("me");
        $pmmpme instanceof Command ? $cmdMap->unregister($pmmpme) : null;
    }

    public function onChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        if (strlen($event->getMessage()) >= 100) {
            $player->sendMessage("§7(§d!§7) §cYou Can't Type More Than 100 Letters At Once!");
            $event->cancel();
            return;
        }

        $profanities = $this->getConfig()->get("profanities");
        if (in_array($event->getMessage(), $profanities) || in_array($event->getMessage(), $this->defaultWords)) {
            $player->sendMessage("§7(§d!§7) §cYou Can't Use Profanities!");
            $event->cancel();
            return;
        }

        $textReplacer = $this->getConfig()->get("TextReplacer");
        foreach ($textReplacer as $var) {
            $message = str_replace($var["Before"], $var["After"], $event->getMessage());
            $event->setMessage($message);
        }
    }
}
