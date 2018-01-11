<?php

namespace DragonsOfMugloar\Entity;


class Dragon extends AbstractPlayer
{

    public function __toString(): string
    {
        return json_encode([
            'dragon' => [
                'scaleThickness' => $this->skills['attack'],
                'clawSharpness'  => $this->skills['armor'],
                'wingStrength'   => $this->skills['agility'],
                'fireBreath'     => $this->skills['endurance'],
            ],
        ]);
    }
}
