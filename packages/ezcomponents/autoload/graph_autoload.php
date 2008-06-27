<?php
/**
 * Autoloader definition for the Graph component.
 *
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1
 * @filesource
 * @package Graph
 */

return array(
    'ezcGraphException'                             => 'Graph/exceptions/exception.php',
    'ezcGraphDatasetAverageInvalidKeysException'    => 'Graph/exceptions/invalid_keys.php',
    'ezcGraphReducementFailedException'             => 'Graph/exceptions/reducement_failed.php',
    'ezcGraphErrorParsingDateException'             => 'Graph/exceptions/date_parsing.php',
    'ezcGraphFlashBitmapBoundingsException'         => 'Graph/exceptions/flash_bitmap_boundings.php',
    'ezcGraphFlashBitmapTypeException'              => 'Graph/exceptions/flash_bitmap_type.php',
    'ezcGraphFontRenderingException'                => 'Graph/exceptions/font_rendering.php',
    'ezcGraphGdDriverUnsupportedImageTypeException' => 'Graph/exceptions/unsupported_image_type.php',
    'ezcGraphInvalidArrayDataSourceException'       => 'Graph/exceptions/invalid_data_source.php',
    'ezcGraphInvalidDisplayTypeException'           => 'Graph/exceptions/invalid_display_type.php',
    'ezcGraphInvalidFontTypeException'              => 'Graph/exceptions/invalid_font.php',
    'ezcGraphInvalidImageFileException'             => 'Graph/exceptions/invalid_image_file.php',
    'ezcGraphMatrixInvalidDimensionsException'      => 'Graph/exceptions/invalid_dimensions.php',
    'ezcGraphMatrixOutOfBoundingsException'         => 'Graph/exceptions/out_of_boundings.php',
    'ezcGraphNoDataException'                       => 'Graph/exceptions/no_data.php',
    'ezcGraphNoSuchDataException'                   => 'Graph/exceptions/no_such_data.php',
    'ezcGraphNoSuchDataSetException'                => 'Graph/exceptions/no_such_dataset.php',
    'ezcGraphNoSuchElementException'                => 'Graph/exceptions/no_such_element.php',
    'ezcGraphOutOfLogithmicalBoundingsException'    => 'Graph/exceptions/out_of_logarithmical_boundings.php',
    'ezcGraphSvgDriverInvalidIdException'           => 'Graph/exceptions/invalid_id.php',
    'ezcGraphTooManyDataSetsExceptions'             => 'Graph/exceptions/too_many_datasets.php',
    'ezcGraphToolsIncompatibleDriverException'      => 'Graph/exceptions/incompatible_driver.php',
    'ezcGraphToolsNotRenderedException'             => 'Graph/exceptions/not_rendered.php',
    'ezcGraphUnknownColorDefinitionException'       => 'Graph/exceptions/unknown_color_definition.php',
    'ezcGraphUnknownFontTypeException'              => 'Graph/exceptions/font_type.php',
    'ezcGraphUnregularStepsException'               => 'Graph/exceptions/unregular_steps.php',
    'ezcGraphInvalidDataException'                  => 'Graph/exceptions/invalid_data.php',
    'ezcGraphChart'                                 => 'Graph/interfaces/chart.php',
    'ezcGraphChartElement'                          => 'Graph/interfaces/element.php',
    'ezcGraphChartOptions'                          => 'Graph/options/chart.php',
    'ezcGraphMatrix'                                => 'Graph/math/matrix.php',
    'ezcGraphAxisLabelRenderer'                     => 'Graph/interfaces/axis_label_renderer.php',
    'ezcGraphChartDataContainer'                    => 'Graph/data_container/base.php',
    'ezcGraphChartElementAxis'                      => 'Graph/element/axis.php',
    'ezcGraphColor'                                 => 'Graph/colors/color.php',
    'ezcGraphCoordinate'                            => 'Graph/structs/coordinate.php',
    'ezcGraphDataSet'                               => 'Graph/datasets/base.php',
    'ezcGraphDataSetProperty'                       => 'Graph/interfaces/dataset_property.php',
    'ezcGraphDriver'                                => 'Graph/interfaces/driver.php',
    'ezcGraphDriverOptions'                         => 'Graph/options/driver.php',
    'ezcGraphLineChart'                             => 'Graph/charts/line.php',
    'ezcGraphPalette'                               => 'Graph/interfaces/palette.php',
    'ezcGraphRadarRenderer'                         => 'Graph/interfaces/radar_renderer.php',
    'ezcGraphRenderer'                              => 'Graph/interfaces/renderer.php',
    'ezcGraphRendererOptions'                       => 'Graph/options/renderer.php',
    'ezcGraphTransformation'                        => 'Graph/math/transformation.php',
    'ezcGraph'                                      => 'Graph/graph.php',
    'ezcGraphArrayDataSet'                          => 'Graph/datasets/array.php',
    'ezcGraphAxisBoxedLabelRenderer'                => 'Graph/renderer/axis_label_boxed.php',
    'ezcGraphAxisCenteredLabelRenderer'             => 'Graph/renderer/axis_label_centered.php',
    'ezcGraphAxisExactLabelRenderer'                => 'Graph/renderer/axis_label_exact.php',
    'ezcGraphAxisNoLabelRenderer'                   => 'Graph/renderer/axis_label_none.php',
    'ezcGraphAxisRadarLabelRenderer'                => 'Graph/renderer/axis_label_radar.php',
    'ezcGraphAxisRotatedLabelRenderer'              => 'Graph/renderer/axis_label_rotated.php',
    'ezcGraphAxisStep'                              => 'Graph/structs/step.php',
    'ezcGraphBarChart'                              => 'Graph/charts/bar.php',
    'ezcGraphBoundings'                             => 'Graph/math/boundings.php',
    'ezcGraphChartElementBackground'                => 'Graph/element/background.php',
    'ezcGraphChartElementDateAxis'                  => 'Graph/axis/date.php',
    'ezcGraphChartElementLabeledAxis'               => 'Graph/axis/labeled.php',
    'ezcGraphChartElementLegend'                    => 'Graph/element/legend.php',
    'ezcGraphChartElementLogarithmicalAxis'         => 'Graph/axis/logarithmic.php',
    'ezcGraphChartElementNumericAxis'               => 'Graph/axis/numeric.php',
    'ezcGraphChartElementText'                      => 'Graph/element/text.php',
    'ezcGraphChartSingleDataContainer'              => 'Graph/data_container/single.php',
    'ezcGraphContext'                               => 'Graph/structs/context.php',
    'ezcGraphDataSetAveragePolynom'                 => 'Graph/datasets/average.php',
    'ezcGraphDataSetBooleanProperty'                => 'Graph/datasets/property/boolean.php',
    'ezcGraphDataSetColorProperty'                  => 'Graph/datasets/property/color.php',
    'ezcGraphDataSetIntProperty'                    => 'Graph/datasets/property/integer.php',
    'ezcGraphDataSetStringProperty'                 => 'Graph/datasets/property/string.php',
    'ezcGraphFlashDriver'                           => 'Graph/driver/flash.php',
    'ezcGraphFlashDriverOptions'                    => 'Graph/options/flash_driver.php',
    'ezcGraphFontOptions'                           => 'Graph/options/font.php',
    'ezcGraphGdDriver'                              => 'Graph/driver/gd.php',
    'ezcGraphGdDriverOptions'                       => 'Graph/options/gd_driver.php',
    'ezcGraphLineChartOptions'                      => 'Graph/options/line_chart.php',
    'ezcGraphLinearGradient'                        => 'Graph/colors/linear_gradient.php',
    'ezcGraphNumericDataSet'                        => 'Graph/datasets/numeric.php',
    'ezcGraphPaletteBlack'                          => 'Graph/palette/black.php',
    'ezcGraphPaletteEz'                             => 'Graph/palette/ez.php',
    'ezcGraphPaletteEzBlue'                         => 'Graph/palette/ez_blue.php',
    'ezcGraphPaletteEzGreen'                        => 'Graph/palette/ez_green.php',
    'ezcGraphPaletteEzRed'                          => 'Graph/palette/ez_red.php',
    'ezcGraphPaletteTango'                          => 'Graph/palette/tango.php',
    'ezcGraphPieChart'                              => 'Graph/charts/pie.php',
    'ezcGraphPieChartOptions'                       => 'Graph/options/pie_chart.php',
    'ezcGraphPolynom'                               => 'Graph/math/polynom.php',
    'ezcGraphRadarChart'                            => 'Graph/charts/radar.php',
    'ezcGraphRadarChartOptions'                     => 'Graph/options/radar_chart.php',
    'ezcGraphRadialGradient'                        => 'Graph/colors/radial_gradient.php',
    'ezcGraphRenderer2d'                            => 'Graph/renderer/2d.php',
    'ezcGraphRenderer2dOptions'                     => 'Graph/options/renderer_2d.php',
    'ezcGraphRenderer3d'                            => 'Graph/renderer/3d.php',
    'ezcGraphRenderer3dOptions'                     => 'Graph/options/renderer_3d.php',
    'ezcGraphRotation'                              => 'Graph/math/rotation.php',
    'ezcGraphSvgDriver'                             => 'Graph/driver/svg.php',
    'ezcGraphSvgDriverOptions'                      => 'Graph/options/svg_driver.php',
    'ezcGraphTools'                                 => 'Graph/tools.php',
    'ezcGraphTranslation'                           => 'Graph/math/translation.php',
    'ezcGraphVector'                                => 'Graph/math/vector.php',
    'ezcGraphVerboseDriver'                         => 'Graph/driver/verbose.php',
);
?>
