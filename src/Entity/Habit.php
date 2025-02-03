<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'habit')]
class Habit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $time = null;  

    #[ORM\Column(type: 'integer')]
    private int $duration;  

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null; 

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $frequency;  

    #[ORM\Column(type: 'array', nullable: true)]
    private ?array $weekDays = null;  

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $googleRecurrenceRule = null;  

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'array')]
    private array $completions = [];  

    public function getId(): ?int { 
        return $this->id; 
    }
    public function getName(): ?string { 
        return $this->name; 
    }
    public function setName(string $name): self { 
        $this->name = $name; return $this; 
    }

    public function getTime(): ?\DateTimeInterface { 
        return $this->time; 
    }
    public function setTime(\DateTimeInterface $time): self { 
        $this->time = $time; return $this; 
    }

    public function getDuration(): int { 
        return $this->duration; 
    }
    public function setDuration(int $duration): self { 
        $this->duration = $duration; return $this; 
    }

    public function getDescription(): ?string { 
        return $this->description;
    }
    public function setDescription(?string $description): self {
        $this->description = $description; return $this; 
    
    }

    public function getUser(): ?User { 
        return $this->user; 
    }
    public function setUser(User $user): self { 
        $this->user = $user; return $this; 
    }

    public function getCreatedAt(): ?\DateTimeInterface { 
        return $this->createdAt; 
    }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { 
        $this->createdAt = $createdAt; return $this; 
    }

    public function getFrequency(): string { 
        return $this->frequency; 
    }
    public function setFrequency(string $frequency): self { 
        $this->frequency = $frequency; return $this;
    }

    public function getWeekDays(): ?array { 
        return $this->weekDays; 
    }

    public function setWeekDays(?array $weekDays): self { 
        $this->weekDays = $weekDays; return $this; 
    }

    public function getGoogleRecurrenceRule(): ?string { 
        return $this->googleRecurrenceRule; 
    
    }
    public function setGoogleRecurrenceRule(?string $googleRecurrenceRule): self { 
        $this->googleRecurrenceRule = $googleRecurrenceRule; return $this; 
    }

    public function addCompletion(\DateTimeInterface $completionDate): self
    {
        $dateString = $completionDate->format('Y-m-d H:i:s');
        if (!in_array($dateString, $this->completions)) {
            $this->completions[] = $dateString;
        }
        return $this;
    }

    public function removeCompletion(\DateTimeInterface $completionDate): self
    {
        $dateString = $completionDate->format('Y-m-d H:i:s');
        $this->completions = array_filter($this->completions, function ($completion) use ($dateString) {
            return $completion !== $dateString;
        });
        return $this;
    }

    public function getCompletions(): array
    {
        return $this->completions;
    }

    public function hasCompletion(\DateTimeInterface $completionDate): bool
    {
        $dateString = $completionDate->format('Y-m-d H:i:s');
      
    }
}
