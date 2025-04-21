<?php namespace ProcessWire;
/**
 * MarkupTwitchStream
 *
 * Returns the status of a Twitch Channel.
 *
 * @author Daniel
 * @version 1.0.0
 * @summary Get the status of a Twitch Channel
 * @href https://github.com/twowheeldev/MarkupTwitchStream
 *
 */
class MarkupTwitchStream extends WireData implements Module
{
    public static function getModuleInfo() {
        return [
            'title' => 'Twitch Stream Status',
            'version' => 1,
            'summary' => 'Provides status of a Twitch Stream',
            'author' => 'TwoWheelDev',
            'autoload' => false,
            'singular' => true,
            'icon' => 'plug',
            'installs' => ['FieldtypeTwitch', 'InputfieldTwitch']
        ];
    }

    public function init()
    {
        $this->set('clientId', $this->get('clientId') ?: '');
        $this->set('clientSecret', $this->get('clientSecret') ?: '');
    }

    public function getAccessToken(): string|bool
    {
        $token = $this->cache->get('twitch_access_token');

        $client = new WireHttp();

        if ($token) {
            $client->setHeader('Authorization', "OAuth $token");
            $response = $client->get('https://id.twitch.tv/oauth2/validate');

            if ($response !== false) {
                return $token;
            } else {
                return false;
            }
        } else {
            $client->setHeader('content-type', 'application/x-www-form-urlencoded');
            $response = $client->post('https://id.twitch.tv/oauth2/token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials',
            ]);
            if ($client->getHttpCode() == 200) {
                $data = json_decode($response, true);
                $token = $data['access_token'];
                
                // $mem->set('twitch_access_token', $token);
                $this->cache->save('twitch_access_token', $token);
                return $token;
            } else {
                $this->logError("Failed to retrieve token: " . $response);
                return false;
            }
        }
    }

    public function getStreamStatus(string $username): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        $client = new WireHttp();
        $client->setHeaders([
            'Client-ID' => $this->clientId,
            'Authorization' => "Bearer $token",
        ]);
        $client->setData(['user_login' => $username]);
        $response = $client->getJSON('https://api.twitch.tv/helix/streams');

        if ($response !== false) {
            return $response['data'][0] ?? null;
        } else {
            $this->logError("Failed to fetch stream status: " . $client->getError());
            return null;
        }
    }

    public function getChannelInfo(string $username): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        $user = $this->getUserInfo($username);
        if (!$user || !isset($user['id'])) return null;

        $client = new WireHttp();
        $client->setHeaders([
            'Client-ID' => $this->clientId,
            'Authorization' => "Bearer $token",
        ]);
        $client->setData(['broadcaster_id' => $user['id']]);
        $response = $client->getJSON('https://api.twitch.tv/helix/channels');

        return $response ?: null;
    }

    public function getUserInfo(string $username): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        $client = new WireHttp();
        $client->setHeaders([
            'Client-ID' => $this->clientId,
            'Authorization' => "Bearer $token",
        ]);
        $client->setData(['login' => $username]);

        $response = $client->getJSON('https://api.twitch.tv/helix/users');

        return $response['data'][0] ?? null;
    }

    private function logError(string $message)
    {
        $this->wire('log')->save('twitch', $message);
    }

    /**
	 * Render twitch markup
	 * 
	 * @param Page $page Page containing Twitch field
	 * @param string $fieldName Name of the twitch field
	 * @return string
	 * 
	 */
    public function render($page, $fieldName) {
        $twitchUsername = $page->get($fieldName);
        $streamStatus = $this->getStreamStatus($twitchUsername);
        $userInfo = $this->getUserInfo($twitchUsername);

        $out = "";
        if ($streamStatus) {
            $stream_thumbnail = str_replace(['{width}', '{height}'], ['590', '332'], $streamStatus['thumbnail_url']);
            $out .= "
			<div class='rounded-2xl overflow-hidden shadow-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white mb-3 border border-zinc-200 dark:border-zinc-700'>
				<img src='" . $stream_thumbnail . "' alt='Stream preview' class='w-full'>
				<div class='p-4'>
					<div class='flex items-center justify-between mb-2'>
						<h3 class='font-bold text-lg'>" . $streamStatus['user_name'] . "</h3>
						<span class='text-red-500 animate-pulse text-sm'>● LIVE</span>
					</div>
					<p class='text-sm text-zinc-900 dark:text-zinc-300'>Game: <span class='italic'>" . $streamStatus['game_name'] . "</span></p>
					<p class='text-xs text-purple-600 dark:text-purple-300 mt-1'>🎥 " . $streamStatus['viewer_count'] . " watching</p>
					<a href='https://twitch.tv/" . $streamStatus['user_name'] . "' target='_blank'
						class='inline-block mt-3 text-white dark:text-zinc-900 bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-sm font-bold'>
						Watch Now
					</a>
				</div>
			</div>
            ";
        } else {
            $out .= "<div class='flex items-center gap-4 p-4 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 rounded-2xl mb-3'>
				<img src='" . $userInfo['profile_image_url'] . "' alt='" . $userInfo['display_name'] . "' class='w-12 h-12 rounded-full opacity-50'>
				<div>
					<p class='text-sm font-semibold'>" . $userInfo['display_name'] . " is offline</p>
					<a href='https://twitch.tv/" . $userInfo['login'] ."' target='_blank' class='text-purple-400 hover:underline text-xs'>
						Follow on Twitch
					</a>
				</div>
			</div>";
        }

        return $out;
    }
}
