<?php

namespace App\Services\HP;

use phpseclib3\Net\SSH2;

class HpService
{

    protected $ssh;

    public function __construct(
        public string $ip,
        public string $username,
        public string $password,
        public ?string $superPassword = null
    ) {
        $this->ssh = new SSH2($this->ip);
    }

    public function connect()
    {
        if (!$this->ssh->login($this->username, $this->password)) {
            throw new \Exception('Neplatné přihlášení');
        }
    }

    public function getRoutingTable(?string $vpnInstance = null, ?string $isbgp = null)
    {
        $this->connect();
        if (!is_null($this->superPassword)) {
            $this->ssh->write('super' . "\n");
            $this->ssh->write($this->superPassword . "\n");
            $this->ssh->read();
        }
        $this->ssh->write('screen-length disable' . "\n");
        $this->ssh->read();

        if(!is_null($isbgp) && !is_null($vpnInstance)) {
            return $this->getVpnBgpRoutes(vpnInstance: $vpnInstance);
        }

        return match ($vpnInstance) {
            null => $this->getRoutes(),
            default => $this->getVpnRoutes($vpnInstance),
        };
    }

    protected function getRoutes()
    {
        $this->ssh->write('disp ip routing-table' . "\n");
        return $this->ssh->read();
    }

    protected function getVpnRoutes(string $vpnInstance)
    {
        $this->ssh->write('disp ip routing-table vpn-instance ' . $vpnInstance . "\n");
        return $this->ssh->read();
    }

    protected function getVpnBgpRoutes(string $vpnInstance)
    {
        $this->ssh->write('disp bgp routing-table ipv4 vpn-instance ' . $vpnInstance . "\n");
        return $this->ssh->read();
    }
}
