<?php

namespace App\Console\Commands;

use App\Services\HP\HpService;
use Illuminate\Console\Command;

class GetSwitchVpnInstanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get-vpn-instance {switchIp} {username} {password} {superPassword=none} {vpnInstance=default} {isbgp=none}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Getting vpn instace from HP switches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connectService = (new HpService(
            ip: $this->argument('switchIp'),
            username: $this->argument('username'),
            password: $this->argument('password'),
            superPassword: $this->argument('superPassword')
        ));

        print_r($connectService->getRoutingTable(vpnInstance:$this->argument('vpnInstance') , isbgp: $this->argument('isbgp')));
    }
}
