<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;
use Illuminate\Support\Facades\Log;

class Screen
{
  public function __construct(protected Client $client)
  {
  }

  public function getClient()
  {
    return $this->client;
  }

  public function cursorPosition(): object
  {
    return (object) $this->client->get("screen/cursor-position")->json();
  }

  public function displays(): array
  {
    return $this->client->get("screen/displays")->json("displays");
  }

  public function primary(): object
  {
    return $this->client->get("screen/primary-display")->json("primaryDisplay");
  }

  public function active(): object|null|array
  {
    return $this->client->get("screen/active")->json();
  }

  /**
   *  Returns the center of the screen where the mouse pointer is placed.
   *
   * @return array<string,int>
   */
  public function getCenterOfActiveScreen(): array
  {
    //get the screen size and current cursor position.
    $cursor = $this->cursorPosition();
    $screen = $this->displays();

    /* Navigate every screen and check for cursor position against the bounds of the screen. */
    $activeScreen = $this->active();
    $resp = $this->client->get("screen/active");
    Log::debug("activeScreen", [
      $activeScreen,
      $resp->body(),
      $resp->headers(),
      $resp->status(),
    ]);

    foreach ($screen as $s) {
      /** @var array $bounds  */
      $bounds = $s["bounds"];
      if (
        $cursor->x >= $bounds["x"] &&
        $cursor->x <= $bounds["x"] + $bounds["width"] &&
        $cursor->y >= $bounds["y"] &&
        $cursor->y <= $bounds["y"] + $bounds["height"]
      ) {
        // I'm inside the display
        return [
          "x" => $bounds["x"] + $bounds["width"] / 2,
          "y" => $bounds["y"] + $bounds["height"] / 2,
        ];
      }
    }
    $bounds = $screen[0]["bounds"];

    return [
      "x" => $bounds["x"] + $bounds["width"] / 2,
      "y" => $bounds["y"] + $bounds["height"] / 2,
    ];
  }
}
