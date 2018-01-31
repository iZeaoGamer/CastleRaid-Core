<?php
/**
 * -==+CastleRaid Core+==-
 * Originally Created by QuiverlyRivarly
 * Originally Created for CastleRaidPE
 *
 * @authors: QuiverlyRivarly and iiFlamiinBlaze
 * @contributors: Nick, Potatoe, and Jason.
 */
declare(strict_types=1);

namespace CRCore;

use CRCore\Commands\{
    ClearInventoryCommand, CustomPotionsCommand, FeedbackCommand, FeedCommand, FlyCommand, HealCommand, InfoCommand, MailCommand, MenuCommand, MPShopCommand, NickCommand, Quests\Quests, Quests\QuestsCommand
};
use CRCore\Events\{
    EventListener, KillMoneyListener, PotionListener, RelicListener
};
use CRCore\Tasks\{
    BroadcastTask, HudTask
};
use pocketmine\{
    plugin\PluginBase, utils\Config
};

class Loader extends PluginBase{

    public function onLoad() : void{
        API::$main = $this;

        $this->saveDefaultConfig();
        $this->saveResource("tsconfig.json");
        $this->saveResource("names.json");
        $this->saveResource("chat.json");
        $this->saveResource("config.json");

        if(file_exists($this->getDataFolder() . "config.json") == true)
            API::$msg = new Config($this->getDataFolder() . "config.json", Config::JSON);

        if(file_exists($this->getDataFolder() . "names.json") == true)
            API::$names = new Config($this->getDataFolder() . "names.json", Config::JSON);

        if(file_exists($this->getDataFolder() . "chat.json") == true)
            API::$chat = new Config($this->getDataFolder() . "chat.json", Config::JSON);

        if(!is_dir($this->getDataFolder() . "/feedback")) @mkdir($this->getDataFolder() . "/feedback");
        if(!is_dir($this->getDataFolder() . "/players")) @mkdir($this->getDataFolder() . "/players");
    }

    public function onEnable() : void{
        new EventListener($this);
        new PotionListener($this);
        new RelicListener($this);
        new KillMoneyListener($this);

        $this->getServer()->getScheduler()->scheduleRepeatingTask(new BroadcastTask($this), 2400);
        //$this->getServer()->getScheduler()->scheduleRepeatingTask(new FakePlayerTask($this), mt_rand(2400, 8400));
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new HudTask($this), 30);

        $this->getServer()->getCommandMap()->registerAll("CRCore", [
            new ClearInventoryCommand($this),
            new CustomPotionsCommand($this),
            new FeedbackCommand($this),
            new FeedCommand($this),
            new FlyCommand($this),
            new HealCommand($this),
            new InfoCommand($this),
            new MailCommand($this),
            new MenuCommand($this),
            new MPShopCommand($this),
            new NickCommand($this),
            new QuestsCommand($this)
        ]);

        $quests = new Quests();
        $quests->registerQuests();
    }
}
