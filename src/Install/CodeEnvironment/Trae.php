<?php

declare(strict_types=1);

namespace Laravel\Boost\Install\CodeEnvironment;

use Laravel\Boost\Contracts\Agent;
use Laravel\Boost\Contracts\McpClient;
use Laravel\Boost\Install\Enums\Platform;

class Trae extends CodeEnvironment implements Agent, McpClient
{
    public bool $useAbsolutePathForMcp = true;
    
    public function name(): string
    {
        return 'trae';
    }

    public function displayName(): string
    {
        return 'Trae';
    }

    public function systemDetectionConfig(Platform $platform): array
    {
        return match ($platform) {
            Platform::Darwin => [
                'paths' => ['/Applications/Trae.app'],
            ],
            Platform::Windows => [
                'paths' => [
                    '%ProgramFiles%\\Trae',
                    '%LOCALAPPDATA%\\Programs\\Trae',
                ],
            ],
            Platform::Linux => [
                // Trae is not supported on Linux
            ],
        };
    }

    public function projectDetectionConfig(): array
    {
        return [
            'paths' => ['.trae'],
        ];
    }

    public function guidelinesPath(): string
    {
        return '.trae/rules/project_rules.md';
    }

    public function frontmatter(): bool
    {
        return false;
    }

    public function mcpConfigPath(): string
    {
        $home = $this->getHomePath();
        
        return match (Platform::current()) {
            Platform::Darwin => $home . '/Library/Application Support/Trae/User/mcp.json',
            Platform::Windows => '%APPDATA%\\Trae\\User\\mcp.json',
            Platform::Linux => '', // Not supported
        };
    }

    private function getHomePath(): string
    {
        if (Platform::current() === Platform::Windows) {
            if (! isset($_SERVER['HOME'])) {
                $_SERVER['HOME'] = $_SERVER['USERPROFILE'];
            }
            $_SERVER['HOME'] = str_replace('\\', '/', $_SERVER['HOME']);
        }
        
        return $_SERVER['HOME'];
    }

    public function mcpConfigKey(): string
    {
        return 'mcpServers';
    }
}
