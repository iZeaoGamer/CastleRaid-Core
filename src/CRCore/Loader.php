<?php
/**
 * -==+CastleRaid Core+==-
 * Originally Created by QuiverlyRivarly
 * Originally Created for CastleRaidPE
 *
 * @authors: CastleRaid Developer Team
 */
declare(strict_types=1);

namespace CRCore;

use CRCore\commands\{
    staff\ClearInventoryCommand,
    guest\CustomPotionsCommand,
    guest\FeedbackCommand,
    staff\FeedCommand,
    staff\FlyCommand,
    staff\HealCommand,
    guest\InfoCommand,
    guest\MenuCommand,
    guest\MPShopCommand,
    guest\NickCommand,
    quests\QuestsCommand,
    quests\Quests
};
use CRCore\events\{
    EventListener,
    PotionListener,
    HeadListener,
    RelicListener,
    KillMoneyListener
};
use CRCore\tasks\{
    BroadcastTask,
    FakePlayerTask,
    HudTask
};
use pocketmine\{
    plugin\PluginBase,
    utils\Config
};

class Loader extends PluginBase{
   
    const CORE_VERSION = "v1.4.6";
   
    public static $instance;
    
    public function onLoad() : void{
        API::$main = $this;
        self::$instance = $this;

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
        new HeadListener($this);
        new RelicListener($this);
        new KillMoneyListener($this);
        new HeadListener($this);

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
    
    public static function getInstance() : self{
        return self::$instance;
    }
}
