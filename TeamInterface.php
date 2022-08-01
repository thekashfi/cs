<?php

interface TeamInterface {
    public function won(): void;

    public function lose(): void;

    public function join_player(string $player): bool;
}