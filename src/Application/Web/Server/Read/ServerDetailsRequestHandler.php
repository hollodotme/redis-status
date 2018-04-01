<?php declare(strict_types=1);

namespace hollodotme\Readis\Application\Web\Server\Read;

use hollodotme\Readis\Application\ReadModel\Queries\FetchServerInformationQuery;
use hollodotme\Readis\Application\ReadModel\QueryHandlers\FetchServerInformationQueryHandler;
use hollodotme\Readis\Application\Web\AbstractRequestHandler;
use hollodotme\Readis\Exceptions\RuntimeException;
use hollodotme\Readis\TwigPage;
use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;

final class ServerDetailsRequestHandler extends AbstractRequestHandler implements HandlesGetRequest
{
	/**
	 * @param ProvidesReadRequestData $request
	 *
	 * @throws RuntimeException
	 */
	public function handle( ProvidesReadRequestData $request )
	{
		$input     = $request->getInput();
		$appConfig = $this->getEnv()->getAppConfig();
		$database  = (string)$input->get( 'database', '0' );
		$serverKey = (string)$input->get( 'serverKey', '0' );

		$query  = new FetchServerInformationQuery( $serverKey );
		$result = (new FetchServerInformationQueryHandler( $this->getEnv() ))->handle( $query );

		if ( $result->failed() )
		{
			$data = ['errorMessage' => $result->getMessage()];
			(new TwigPage())->respond( 'Theme/Error.twig', $data, 500 );

			return;
		}

		$serverInformation = $result->getServerInformation();

		$data = [
			'appConfig'      => $appConfig,
			'database'       => $database,
			'serverKey'      => $serverKey,
			'server'         => $serverInformation->getServer(),
			'serverConfig'   => $serverInformation->getServerConfig(),
			'slowLogCount'   => $serverInformation->getSlowLogCount(),
			'slowLogEntries' => $serverInformation->getSlowLogEntries(),
			'serverInfo'     => $serverInformation->getServerInfo(),
		];

		(new TwigPage())->respond( 'Server/Read/Pages/ServerDetails.twig', $data );
	}
}
