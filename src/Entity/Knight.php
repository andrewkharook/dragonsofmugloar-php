<?php

namespace DragonsOfMugloar\Entity;


use Symfony\Component\OptionsResolver\OptionsResolver;

class Knight extends AbstractPlayer
{

    protected function configureSkills(OptionsResolver $resolver): void
    {
        parent::configureSkills($resolver);

        $resolver->setDefined('name');
    }

    public function getSkills(): array
    {
        $skills = $this->skills;
        unset($skills['name']);
        return $skills;
    }

    public function __toString(): string
    {
        return json_encode([
            'knight' => [
                'attack'    => $this->skills['attack'],
                'armor'     => $this->skills['armor'],
                'agility'   => $this->skills['agility'],
                'endurance' => $this->skills['endurance'],
            ],
        ]);
    }
}
