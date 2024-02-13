<?php

namespace Erla\WpToolsetFixtures\Creator;
use Erla\WpToolsetFixtures\Vault\IdVault;

class PostsCreator extends AbstractCreator
{
    private IdVault $vault;

    public function __construct(IdVault $vault) {
        $this->vault = $vault;
    }
    public function create(string $jsonFilePath): void
    {
        foreach ($this->getFixtures($jsonFilePath) as $post) {
            $guid = $post['guid'];
            $parentGuid = $post['post_parent_guid'] ?? null;
            $parentId = $parentGuid ? $this->vault->get($parentGuid) : null;
            $type = $post['type'] ?? 'post';
            $title = $this->secureArg($post['post_title']);
            $content = $this->secureArg($post['content']);
            $categories = $post['categories']
                ? implode(',', array_map(function($uuid) { return $this->vault->get($uuid); }, $post['categories']))
                : null;

            $args = [
                "--post_type=$type",
                "--post_title=$title",
                "--post_content=$content",
                "--guid=$guid",
                $parentId ? "--post_parent=$parentId" : '',
                $categories ? "--post_category=$categories" : '',
                "--post_status=publish",
                "--porcelain",
                "--allow-root",
            ];
            $result = $this->cliRun(
                $this->buildCommand('post create', $args)
            );
            if ($result) {
                $this->cliSuccess("Dodano post: $title");
                $this->vault->set($guid, $result);
            } else {
                $this->cliFailure("Błąd podczas dodawania posta: $title");
            }
        }
    }

    public function greet(): void
    {
        $this->cliLine('*** Rozpoczynam generowanie postów');
    }
}
