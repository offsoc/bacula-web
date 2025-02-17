<?php

/**
 * Copyright (C) 2010-present Davide Franco
 *
 * This file is part of the Bacula-Web project.
 *
 * Bacula-Web is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Bacula-Web is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with Bacula-Web. If not, see
 * <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Entity\Bacula;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Bacula\Repository\LogRepository;

/**
 * @ORM\Entity(repositoryClass=LogRepository::class)
 * @ORM\Table(name="Log")
 */
class Log
{
    /**
     * @ORM\Column(type="integer", name="LogId")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     *
     * @var int
     */
    private int $id;

    /**
     * @ORM\Column(type="integer", name="JobId")
     *
     * @var int
     */
    private int $jobid;

    /**
     * @ORM\Column(type="text", name="LogText")
     *
     * @var string
     */
    private string $logtext;

    /**
     * @ORM\Column(type="string", name="Time")
     *
     * @var string $time
     */
    private string $time;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bacula\Job", inversedBy="logs")
     * @ORM\JoinColumn(name="JobId", referencedColumnName="JobId")
     *
     * @var Job
     */
    private Job $job;

    /**
     * @return string
     */
    public function getLogText(): string
    {
        return $this->logtext;
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getJobid(): int
    {
        return $this->jobid;
    }
}
