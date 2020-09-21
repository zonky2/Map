<?php

class MapViewer extends ContentElement
{
	protected $strTemplate = 'ce_mapviewer';

	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objMap = \MapModel::findByPK($this->map);
			$objTemplate = new \BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['tl_content']['map_legend']) . ' ###';
			$objTemplate->title = '['. $objMap->id.'] - '. $objMap->title;
			return $objTemplate->parse();	
		}
		return parent::generate();
	}//end generate

	protected function compile()
	{

		global $objPage;
		$this->loadLanguageFile('tl_map');
		$this->loadLanguageFile('tl_map_points');

		//gets the categorie
		$objMap = \MapModel::findByPK($this->map);

		$GLOBALS['TL_JAVASCRIPT'][] = '//www.openlayers.org/api/OpenLayers.js';

		try
		{
			$mapposition = unserialize($objMap->position);
		}
		catch (Exception $e)
		{
			$mapposition = array();
		}

		$Map= array(
			id => $objMap->id,
			title => $objMap->title,
			description => $objMap->description,
			height => $objMap->height,
			latitude  => $mapposition[0],
			longitude  => $mapposition[1],
			zoom  => $mapposition[2],
			autozoom => boolval($objMap->autozoom)
		);

		$this->Template->Map = $Map;

		$filter = array('column' => array('pid=?','published=?'),'value' => array($objMap->id,1));
		$objPoints = \MapPointsModel::findAll($filter);

		$points = array();	


		foreach ($objPoints as $key => $value) {

			try
			{
				$position = unserialize($value->position);
			}
			catch (Exception $e)
			{
				$position = array();
			}

			$points[$key] = array(
				title => $value->title,
				image => \FilesModel::findByPk($value->image)->path,
				latitude  => $position[0],
				longitude  => $position[1],
				zoom  => $position[2],
				description =>  $value->description,
				info => boolval($value->info)
			);
		}
		
		$this->Template->Points = $points;

	}//end compile

}//end class
