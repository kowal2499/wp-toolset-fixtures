<?php

namespace Erla\WpToolsetFixtures\Creator;
use Erla\WpToolsetFixtures\Vault\IdVault;

class MenuCreator extends AbstractCreator
{
    private IdVault $vault;
    public function __construct(IdVault $vault)
    {
        $this->vault = $vault;
    }

    public function create(string $jsonFilePath): void
    {
        foreach ($this->getFixtures($jsonFilePath) as $menu) {
            $name = $this->secureArg($menu['name']);

            $args = [$name, '--porcelain', '--allow-root'];
            $menuWpId = $this->cliRun(
                $this->buildCommand('menu create', $args)
            );

            if ($menuWpId) {
                $this->cliSuccess("Dodano menu: $name");
            } else {
                $this->cliFailure("Błąd podczas dodawania menu: $name");
                continue;
            }

            foreach ($menu['items'] as $item) {
                $title = $this->secureArg($item['title']);
                $menuGuid = $item['guid'] ?? '';

                try {
                    if ($item['post_guid']) {
                        $command = $this->getCommandForPost($menuWpId, $item);
                    }
                    else if ($item['term_guid']) {
                        $command = $this->getCommandForTerm($menuWpId, $item);
                    }
                    else if ($item['link']) {
                        $command = $this->getCommandForLink($menuWpId, $item);
                    } else {
                        throw new \Exception("-- błąd dodawania pozycji menu: $title, nieobsługiwany typ pozycji menu");
                    }

                    if (!$menuItemId = $this->cliRun($command)) {
                        throw new \Exception("-- błąd dodawania pozycji menu: $title");
                    }

                    $this->cliSuccess("-- dodano pozycję menu: $title");
                    $this->vault->set($menuGuid, $menuItemId);

                } catch (\Exception $e) {
                    $this->cliFailure($e->getMessage());
                }
            }
        }
    }

    public function greet(): void
    {
        $this->cliLine('*** Rozpoczynam generowanie menu');
    }

    private function getCommandForPost(int $menuId, array $itemData): string
    {
        $postGuid = $itemData['post_guid'];
        $parentGuid = $itemData['parent_guid'] ?? null;
        $parentId = $parentGuid ? $this->vault->get($parentGuid) : null;
        $title = $this->secureArg($itemData['title']);

        if (!$postId = $this->vault->get($postGuid)) {
            throw new \Exception("Strona: \"$postGuid\" nie istnieje");
        }

        $args = [
            $menuId,
            $postId,
            $parentId ? "--parent-id=$parentId" : null,
            "--title=$title",
            '--porcelain',
            '--allow-root',
        ];

        return $this->buildCommand('menu item add-post', $args);
    }

    private function getCommandForTerm(int $menuId, array $itemData): string
    {
        $parentGuid = $itemData['parent_guid'] ?? null;
        $parentId = $parentGuid ? $this->vault->get($parentGuid) : null;
        $taxonomy = $itemData['taxonomy'];
        $termGuid = $itemData['term_guid'];
        if (!$termId = $this->vault->get($termGuid)) {
            throw new \Exception("Term: \"$termGuid\" nie istnieje");
        }

        $title = $this->secureArg($itemData['title']);

        $args = [
            $menuId,
            $taxonomy,
            $termId,
            $parentId ? "--parent-id=$parentId" : null,
            "--title=$title",
            '--porcelain',
            '--allow-root',
        ];

        return $this->buildCommand('menu item add-term', $args);
    }

    private function getCommandForLink(int $menuId, array $itemData): string
    {
        $parentGuid = $itemData['parent_guid'] ?? null;
        $parentId = $parentGuid ? $this->vault->get($parentGuid) : null;
        $title = $this->secureArg($itemData['title']);
        $link = $this->secureArg($itemData['link']);

        $args = [
            $menuId,
            $title,
            $link,
            $parentId ? "--parent-id=$parentId" : null,
            '--porcelain',
            '--allow-root',
        ];

        return $this->buildCommand('menu item add-custom', $args);
    }
}
