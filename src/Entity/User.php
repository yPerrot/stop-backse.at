<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private ?string $twitchId;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $displayedUsername;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $twitchToken;


    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $understood = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getTwitchId(): ?string
    {
        return $this->twitchId;
    }

    public function setTwitchId(string $twitchId): self
    {
        $this->twitchId = $twitchId;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayedUsername(): string
    {
        return $this->displayedUsername;
    }

    /**
     * @param string $displayedUsername
     * @return User
     */
    public function setDisplayedUsername(string $displayedUsername): User
    {
        $this->displayedUsername = $displayedUsername;
        return $this;
    }

    /**
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     * @return User
     */
    public function setAvatar(string $avatar): User
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return string
     */
    public function getTwitchToken(): string
    {
        return $this->twitchToken;
    }

    /**
     * @param string $twitchToken
     * @return User
     */
    public function setTwitchToken(string $twitchToken): User
    {
        $this->twitchToken = $twitchToken;
        return $this;
    }

    public function getPassword(): void
    {
    }

    public function getSalt(): void
    {
    }

    public function eraseCredentials(): void
    {
    }

    public function getUnderstood(): ?bool
    {
        return $this->understood;
    }

    public function setUnderstood(bool $understood): self
    {
        $this->understood = $understood;

        return $this;
    }
}
