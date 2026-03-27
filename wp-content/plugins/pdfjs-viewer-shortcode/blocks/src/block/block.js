const { __ } = wp.i18n;

import './editor.scss';
import './style.scss';

const { registerBlockType } = wp.blocks;
const { MediaUpload, InspectorControls } = wp.blockEditor;

const {
	Button,
	PanelRow,
	PanelBody,
	ToggleControl,
	RangeControl,
	SelectControl,
	TextControl,
} = wp.components;

const defaultHeight = 800;
const defaultWidth = 0;

const ALLOWED_MEDIA_TYPES = [ 'application/pdf' ];

// Safe access to localized options with fallbacks
const pdfjsOpts = window.pdfjs_options || {};

registerBlockType( 'pdfjsblock/pdfjs-embed', {
	title: __( 'Embed PDF.js Viewer', 'pdfjs-viewer-shortcode' ),
	icon: 'media-document',
	category: 'common',
	attributes: {
		imageURL: {
			type: 'string',
		},
		imgID: {
			type: 'number',
		},
		imgTitle: {
			type: 'string',
			default: 'PDF File',
		},
		showDownload: {
			type: 'boolean',
			default: !! pdfjsOpts.pdfjs_download_button,
		},
		showPrint: {
			type: 'boolean',
			default: !! pdfjsOpts.pdfjs_print_button,
		},
		showFullscreen: {
			type: 'boolean',
			default: !! pdfjsOpts.pdfjs_fullscreen_link,
		},
		openFullscreen: {
			type: 'boolean',
			default: !! pdfjsOpts.pdfjs_fullscreen_link_target,
		},
		fullscreenText: {
			type: 'string',
			default: pdfjsOpts.pdfjs_fullscreen_link_text || 'View Fullscreen',
		},
		viewerHeight: {
			type: 'number',
			default: pdfjsOpts.pdfjs_embed_height
				? Number( pdfjsOpts.pdfjs_embed_height )
				: 800,
		},
		viewerWidth: {
			type: 'number',
			default: pdfjsOpts.pdfjs_embed_width
				? Number( pdfjsOpts.pdfjs_embed_width )
				: 0,
		},
		viewerScale: {
			type: 'string',
			default: pdfjsOpts.pdfjs_viewer_scale || 'auto',
		},
	},
	keywords: [ __( 'PDF Selector', 'pdfjs-viewer-shortcode' ) ],

	edit( props ) {
		const onFileSelect = ( img ) => {
			props.setAttributes( {
				imageURL: img.url,
				imgID: img.id,
				imgTitle: img.title,
			} );
		};

		const onRemoveImg = () => {
			props.setAttributes( {
				imageURL: null,
				imgID: null,
				imgTitle: null,
			} );
		};

		const onToggleDownload = ( value ) => {
			props.setAttributes( {
				showDownload: value,
			} );
		};

		const onTogglePrint = ( value ) => {
			props.setAttributes( {
				showPrint: value,
			} );
		};

		const onToggleFullscreen = ( value ) => {
			props.setAttributes( {
				showFullscreen: value,
			} );
		};

		const onToggleOpenFullscreen = ( value ) => {
			props.setAttributes( {
				openFullscreen: value,
			} );
		};

		const onHeightChange = ( value ) => {
			// handle the reset button
			if ( undefined === value ) {
				value = defaultHeight;
			}
			props.setAttributes( {
				viewerHeight: value,
			} );
		};

		const onWidthChange = ( value ) => {
			// handle the reset button
			if ( undefined === value ) {
				value = defaultWidth;
			}
			props.setAttributes( {
				viewerWidth: value,
			} );
		};

		const onFullscreenTextChange = ( value ) => {
			value = value.replace( /(<([^>]+)>)/gi, '' );
			props.setAttributes( {
				fullscreenText: value,
			} );
		};

		// Compute preview iframe src and width for editor preview
		const viewerBase = pdfjsOpts.pdfjs_viewer_url || null;

		// Build viewer URL with current block settings for live preview
		let iframeSrc = '';
		if ( props.attributes.imageURL && viewerBase ) {
			const params = new URLSearchParams( {
				file: props.attributes.imageURL,
				attachment_id: props.attributes.imgID || '',
				dButton: props.attributes.showDownload ? 'true' : 'false',
				pButton: props.attributes.showPrint ? 'true' : 'false',
				oButton: 'false',
				editButtons:
					pdfjsOpts.pdfjs_editing_buttons === 'on' ? 'true' : 'false',
				sButton:
					pdfjsOpts.pdfjs_search_button === 'on' ? 'true' : 'false',
			} );
			// Build hash with zoom and pagemode (always include to override stored preferences)
			const zoom = pdfjsOpts.pdfjs_viewer_scale || 'auto';
			const pagemode = pdfjsOpts.pdfjs_viewer_pagemode || 'none';
			const hash = `zoom=${ encodeURIComponent(
				zoom
			) }&pagemode=${ encodeURIComponent( pagemode ) }`;
			iframeSrc = `${ viewerBase }?${ params.toString() }#${ hash }`;
		} else if ( props.attributes.imageURL ) {
			iframeSrc = props.attributes.imageURL;
		}

		const viewerWidthAttr =
			props.attributes.viewerWidth === undefined ||
			props.attributes.viewerWidth === 0
				? '100%'
				: `${ props.attributes.viewerWidth }px`;

		return [
			<InspectorControls key="i1">
				<PanelBody
					title={ __( 'PDF.js Options', 'pdfjs-viewer-shortcode' ) }
				>
					<PanelRow>
						<ToggleControl
							label={ __(
								'Show Save Option',
								'pdfjs-viewer-shortcode'
							) }
							help={
								props.attributes.showDownload
									? __( 'Yes', 'pdfjs-viewer-shortcode' )
									: __( 'No', 'pdfjs-viewer-shortcode' )
							}
							checked={ props.attributes.showDownload }
							onChange={ onToggleDownload }
						/>
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={ __(
								'Show Print Option',
								'pdfjs-viewer-shortcode'
							) }
							help={
								props.attributes.showPrint
									? __( 'Yes', 'pdfjs-viewer-shortcode' )
									: __( 'No', 'pdfjs-viewer-shortcode' )
							}
							checked={ props.attributes.showPrint }
							onChange={ onTogglePrint }
						/>
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={ __(
								'Show Fullscreen Option',
								'pdfjs-viewer-shortcode'
							) }
							help={
								props.attributes.showFullscreen
									? __( 'Yes', 'pdfjs-viewer-shortcode' )
									: __( 'No', 'pdfjs-viewer-shortcode' )
							}
							checked={ props.attributes.showFullscreen }
							onChange={ onToggleFullscreen }
						/>
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={ __(
								'Open Fullscreen in new tab?',
								'pdfjs-viewer-shortcode'
							) }
							help={
								props.attributes.openFullscreen
									? __( 'Yes', 'pdfjs-viewer-shortcode' )
									: __( 'No', 'pdfjs-viewer-shortcode' )
							}
							checked={ props.attributes.openFullscreen }
							onChange={ onToggleOpenFullscreen }
						/>
					</PanelRow>
					<PanelRow>
						<TextControl
							label="Fullscreen Text"
							value={ props.attributes.fullscreenText }
							onChange={ onFullscreenTextChange }
						/>
					</PanelRow>
				</PanelBody>
				<PanelBody
					title={ __( 'Embed Height', 'pdfjs-viewer-shortcode' ) }
				>
					<RangeControl
						label={ __(
							'Viewer Height (pixels)',
							'pdfjs-viewer-shortcode'
						) }
						value={ props.attributes.viewerHeight }
						onChange={ onHeightChange }
						min={ 0 }
						max={ 5000 }
						allowReset={ true }
						initialPosition={ defaultHeight }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Embed Width', 'pdfjs-viewer-shortcode' ) }
				>
					<RangeControl
						label={ __(
							'Viewer Width (pixels)',
							'pdfjs-viewer-shortcode'
						) }
						help="By default 0 will be 100%."
						value={ props.attributes.viewerWidth }
						onChange={ onWidthChange }
						min={ 0 }
						max={ 5000 }
						allowReset={ true }
						initialPosition={ defaultWidth }
					/>
				</PanelBody>
			</InspectorControls>,
			<div className="pdfjs-wrapper" key="i2">
				<div className="pdfjs-header">
					<strong>
						{ __( 'PDF.js Embed', 'pdfjs-viewer-shortcode' ) }
					</strong>
					&nbsp; - &nbsp;
					<span className="pdfjs-title">
						{ props.attributes.imgTitle
							? props.attributes.imgTitle
							: 'Choose a PDF file' }
					</span>
					&nbsp; - &nbsp;
					{ props.attributes.imageURL ? (
						<Button
							className="pdfjs-button"
							onClick={ onRemoveImg }
							aria-label={ __(
								'Remove the current PDF file',
								'pdfjs-viewer-shortcode'
							) }
						>
							{ __( 'Remove PDF', 'pdfjs-viewer-shortcode' ) }
						</Button>
					) : (
						<MediaUpload
							onSelect={ onFileSelect }
							allowedTypes={ ALLOWED_MEDIA_TYPES }
							value={ props.attributes.imgID }
							render={ ( { open } ) => (
								<Button
									className="pdfjs-button"
									onClick={ open }
									aria-label={ __(
										'Open media library to choose a PDF file',
										'pdfjs-viewer-shortcode'
									) }
								>
									{ __(
										'Choose a PDF file',
										'pdfjs-viewer-shortcode'
									) }
								</Button>
							) }
						/>
					) }
				</div>
				{ props.attributes.imageURL ? (
					<div style={ { width: '100%' } }>
						{ /* Editor preview iframe */ }
						<div
							className="pdfjs-preview"
							width={ viewerWidthAttr ? viewerWidthAttr : '100%' }
							style={ { maxWidth: '100%' } }
							height={
								props.attributes.viewerHeight || defaultHeight
							}
							role="region"
							aria-label={ __(
								'PDF Preview',
								'pdfjs-viewer-shortcode'
							) }
						>
							<div className="pdfjs-preview-link">
								{ props.attributes.showFullscreen.toString() ===
									'true' && props.attributes.fullscreenText }
							</div>
							<iframe
								src={ iframeSrc }
								width={ viewerWidthAttr }
								height={
									props.attributes.viewerHeight ||
									defaultHeight
								}
								className="pdfjs-iframe-editor"
								title={
									props.attributes.imgTitle ||
									__(
										'PDF Preview',
										'pdfjs-viewer-shortcode'
									)
								}
								aria-label={
									props.attributes.imgTitle
										? `${ __(
												'PDF document preview',
												'pdfjs-viewer-shortcode'
										  ) }: ${ props.attributes.imgTitle }`
										: __(
												'PDF document preview',
												'pdfjs-viewer-shortcode'
										  )
								}
								tabIndex="0"
								style={ {
									border: '1px solid #ddd',
									maxWidth: '100%',
								} }
							/>
						</div>
					</div>
				) : null }
			</div>,
		];
	},

	save( props ) {
		return (
			<div className="pdfjs-wrapper">
				{ `[pdfjs-viewer attachment_id=${
					props.attributes.imgID
				} url=${ props.attributes.imageURL } viewer_width=${
					props.attributes.viewerWidth !== undefined
						? props.attributes.viewerWidth
						: defaultWidth
				} viewer_height=${
					props.attributes.viewerHeight !== undefined
						? props.attributes.viewerHeight
						: defaultHeight
				} download=${ props.attributes.showDownload.toString() } print=${ props.attributes.showPrint.toString() } fullscreen=${ props.attributes.showFullscreen.toString() } fullscreen_target=${ props.attributes.openFullscreen.toString() } fullscreen_text="${
					props.attributes.fullscreenText
				}"]` }
			</div>
		);
	},
} );
