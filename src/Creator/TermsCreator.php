<?php

namespace Erla\WpToolsetFixtures\Creator;
use Erla\WpToolsetFixtures\Vault\IdVault;

class TermsCreator extends AbstractCreator
{
    private IdVault $vault;

    public function __construct(IdVault $vault) {
        $this->vault = $vault;
    }

    public function create(string $jsonFilePath): void
    {
        foreach ($this->getFixtures($jsonFilePath) as $term) {
            $guid = $term['guid'];
            $taxonomy = $term['taxonomy'];
            $name = $this->secureArg($term['name'] ?? '');
            $slug = $this->secureArg($term['slug'] ?? '');
            $description = $this->secureArg($term['description'] ?? '');
            $parentGuid = $term['parentGuid'] ?? null;

            $parentId = $parentGuid ? $this->vault->get($parentGuid) : null;

            $args = [
                $taxonomy,
                $name,
                $parentId ? "--parent=$parentId" : '',
                "--slug=$slug",
                "--description=$description",
                "--porcelain",
                "--allow-root",
            ];

            $result = $this->cliRun(
                $this->buildCommand('term create', $args)
            );
            if ($result) {
                $this->cliSuccess("Dodano term: $name");
                $this->vault->set($guid, $result);
            } else {
                $this->cliFailure("Błąd podczas dodawania term: $name");
            }
        }
    }

    public function greet(): void
    {
        $this->cliLine('*** Rozpoczynam generowanie terms');
    }
}