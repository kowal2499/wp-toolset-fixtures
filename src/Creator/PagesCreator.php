<?php

namespace Erla\WpToolsetFixtures\Creator;
use Erla\WpToolsetFixtures\Vault\IdVault;

class PagesCreator extends AbstractCreator
{
    private IdVault $vault;

    public function __construct(IdVault $vault) {
        $this->vault = $vault;
    }
    private array $pagesMap = [];

    public function create(string $jsonFilePath): void
    {
        foreach ($this->getFixtures($jsonFilePath) as $page) {
            $guid = $page['guid'];
            $parentGuid = $page['post_parent_guid'] ?? null;
            $parentId = $parentGuid ? $this->vault->get($parentGuid) : null;
            $title = $this->secureArg($page['post_title']);
            $content = $this->secureArg($page['content']);

            $args = [
                "--post_type=page",
                "--post_title=$title",
                "--post_content=$content",
                "--guid=$guid",
                $parentId ? "--post_parent=$parentId" : '',
                "--post_status=publish",
                "--porcelain",
                "--allow-root",
            ];
            $result = $this->cliRun(
                $this->buildCommand('post create', $args)
            );
            if ($result) {
                $this->cliSuccess("Dodano stronę: $title");
                $this->vault->set($guid, $result);
            } else {
                $this->cliFailure("Błąd podczas dodawania strony: $title");
            }
        }
    }

    public function greet(): void
    {
        $this->cliLine('*** Rozpoczynam generowanie stron');
    }
}
