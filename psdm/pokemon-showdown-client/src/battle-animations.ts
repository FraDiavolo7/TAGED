/**
 * Pokemon Showdown Battle Animations
 *
 * There are the specific resource files and scripts for misc animations
 *
 * Licensing note: PS's client has complicated licensing:
 * - The client as a whole is AGPLv3
 * - The battle replay/animation engine (battle-*.ts) by itself is MIT
 *
 * @author Guangcong Luo <guangcongluo@gmail.com>
 * @license MIT
 */

/*

Most of this file is: CC0 (public domain)
  <http://creativecommons.org/publicdomain/zero/1.0/>

This license DOES extend to all images in the fx/ folder, with the exception of icicle.png, lightning.png, and bone.png.

icicle.png and lightning.png by Clint Bellanger are triple-licensed GPLv2/GPLv3/CC-BY-SA-3.0.
  <http://opengameart.org/content/icicle-spell>
  <http://opengameart.org/content/lightning-shock-spell>

rocks.png, rock1.png, rock2.png by PO user "Gilad" is licensed GPLv3.

This license DOES NOT extend to any images in the sprites/ folder.

This license DOES NOT extend to any other files in this repository.

*/

class BattleScene {
	battle: Battle;
	animating = true;
	acceleration = 1;

	/** Note: Not the actual generation of the battle, but the gen of the sprites/background */
	gen = 7;
	/** 1 = singles, 2 = doubles, 3 = triples */
	activeCount = 1;

	numericId = 0;
	$frame: JQuery<HTMLElement>;
	$battle: JQuery<HTMLElement> = null!;
	$logFrame: JQuery<HTMLElement>;
	$options: JQuery<HTMLElement> = null!;
	$logPreempt: JQuery<HTMLElement> = null!;
	$log: JQuery<HTMLElement> = null!;
	$terrain: JQuery<HTMLElement> = null!;
	$weather: JQuery<HTMLElement> = null!;
	$bgEffect: JQuery<HTMLElement> = null!;
	$bg: JQuery<HTMLElement> = null!;
	$sprite: JQuery<HTMLElement> = null!;
	$sprites: [JQuery<HTMLElement>, JQuery<HTMLElement>] = [null!, null!];
	$spritesFront: [JQuery<HTMLElement>, JQuery<HTMLElement>] = [null!, null!];
	$stat: JQuery<HTMLElement> = null!;
	$fx: JQuery<HTMLElement> = null!;
	$leftbar: JQuery<HTMLElement> = null!;
	$rightbar: JQuery<HTMLElement> = null!;
	$turn: JQuery<HTMLElement> = null!;
	$messagebar: JQuery<HTMLElement> = null!;
	$delay: JQuery<HTMLElement> = null!;
	$hiddenMessage: JQuery<HTMLElement> = null!;

	sideConditions = [{}, {}] as [{[id: string]: Sprite[]}, {[id: string]: Sprite[]}];

	preloadDone = 0;
	preloadNeeded = 0;
	bgm: string | null = null;
	backdropImage: string = '';
	bgmNum = 0;
	preloadCache = {} as {[url: string]: HTMLImageElement};

	autoScrollOnResume = false;
	messagebarOpen = false;
	interruptionCount = 1;
	curWeather = '';
	curTerrain = '';

	// Animation state
	////////////////////////////////////

	timeOffset = 0;
	pokemonTimeOffset = 0;
	minDelay = 0;
	/** jQuery objects that need to finish animating */
	activeAnimations = $();

	constructor(battle: Battle, $frame: JQuery<HTMLElement>, $logFrame: JQuery<HTMLElement>) {
		this.battle = battle;
		$frame.addClass('battle');
		this.$frame = $frame;
		this.$logFrame = $logFrame;

		let numericId = 0;
		if (battle.id) {
			numericId = parseInt(battle.id.slice(battle.id.lastIndexOf('-') + 1));
		}
		if (!numericId) {
			numericId = Math.floor(Math.random() * 1000000);
		}
		this.numericId = numericId;

		this.preloadEffects();
		// reset() is called during battle initialization, so it doesn't need to be called here
	}

	reset() {
		this.updateGen();

		// Log frame
		/////////////

		if (this.$options) {
			this.$log.empty();
			this.$logPreempt.empty();
		} else {
			this.$logFrame.empty();
			this.$options = $('<div class="battle-options"></div>');
			this.$log = $('<div class="inner" role="log"></div>');
			this.$logPreempt = $('<div class="inner-preempt"></div>');
			this.$logFrame.append(this.$options);
			this.$logFrame.append(this.$log);
			this.$logFrame.append(this.$logPreempt);
		}

		// Battle frame
		///////////////

		this.$frame.empty();
		this.$battle = $('<div class="innerbattle"></div>');
		this.$frame.append(this.$battle);

		this.$bg = $('<div class="backdrop" style="background-image:url(' + Tools.resourcePrefix + this.backdropImage + ');display:block;opacity:0.8"></div>');
		this.$terrain = $('<div class="weather"></div>');
		this.$weather = $('<div class="weather"></div>');
		this.$bgEffect = $('<div></div>');
		this.$sprite = $('<div></div>');

		this.$sprites = [$('<div></div>'), $('<div></div>')];
		this.$spritesFront = [$('<div></div>'), $('<div></div>')];

		this.$sprite.append(this.$sprites[1]);
		this.$sprite.append(this.$spritesFront[1]);
		this.$sprite.append(this.$spritesFront[0]);
		this.$sprite.append(this.$sprites[0]);

		this.$stat = $('<div role="complementary" aria-label="Active Pokemon"></div>');
		this.$fx = $('<div></div>');
		this.$leftbar = $('<div class="leftbar" role="complementary" aria-label="Your ReplayParser"></div>');
		this.$rightbar = $('<div class="rightbar" role="complementary" aria-label="Opponent\'s ReplayParser"></div>');
		this.$turn = $('<div></div>');
		this.$messagebar = $('<div class="messagebar message"></div>');
		this.$delay = $('<div></div>');
		this.$hiddenMessage = $('<div class="message" style="position:absolute;display:block;visibility:hidden"></div>');

		this.$battle.append(this.$bg);
		this.$battle.append(this.$terrain);
		this.$battle.append(this.$weather);
		this.$battle.append(this.$bgEffect);
		this.$battle.append(this.$sprite);
		this.$battle.append(this.$stat);
		this.$battle.append(this.$fx);
		this.$battle.append(this.$leftbar);
		this.$battle.append(this.$rightbar);
		this.$battle.append(this.$turn);
		this.$battle.append(this.$messagebar);
		this.$battle.append(this.$delay);
		this.$battle.append(this.$hiddenMessage);

		if (this.battle.ignoreNicks) {
			this.$log.addClass('hidenicks');
			this.$messagebar.addClass('hidenicks');
			this.$hiddenMessage.addClass('hidenicks');
		}

		if (!this.animating) {
			this.$battle.append('<div class="seeking"><strong>seeking...</strong></div>');
		}

		this.messagebarOpen = false;
		this.timeOffset = 0;
		this.pokemonTimeOffset = 0;
	}

	animationOff() {
		this.$battle.append('<div class="seeking"><strong>seeking...</strong></div>');
		this.stopAnimation();

		this.animating = false;
		this.autoScrollOnResume = (this.$logFrame.scrollTop()! + 60 >= this.$log.height()! + this.$logPreempt.height()! - this.$options.height()! - this.$logFrame.height()!);
		this.$messagebar.empty().css({
			opacity: 0,
			height: 0
		});
	}
	stopAnimation() {
		this.interruptionCount++;
		this.$battle.find(':animated').finish();
		this.$fx.empty();
	}
	animationOn() {
		if (this.animating) return;
		this.animating = true;
		this.$battle.find('.seeking').remove();
		this.updateSidebars();
		for (const side of this.battle.sides) {
			for (const pokemon of side.pokemon) {
				pokemon.sprite.reset(pokemon);
			}
		}
		this.updateWeather(true);
		this.resetTurn();
		if (this.autoScrollOnResume) {
			this.$logFrame.scrollTop(this.$log.height()! + this.$logPreempt.height()!);
		}
		this.resetSideConditions();
	}
	pause() {
		this.stopAnimation();
		this.soundPause();
		if (this.battle.resumeButton) {
			this.$frame.append('<div class="playbutton"><button data-action="resume"><i class="fa fa-play icon-play"></i> Resume</button></div>');
			this.$frame.find('div.playbutton button').click(this.battle.resumeButton);
		}
	}
	resume() {
		this.$frame.find('div.playbutton').remove();
		this.soundStart();
	}
	wait(time: number) {
		if (!this.animating) return;
		this.timeOffset += time;
	}

	// Sprite handling
	/////////////////////////////////////////////////////////////////////

	addSprite(sprite: PokemonSprite) {
		if (sprite.$el) this.$sprites[sprite.siden].append(sprite.$el);
	}
	showEffect(effect: string | SpriteData, start: ScenePos, end: ScenePos, transition: string, after?: string) {
		if (typeof effect === 'string') effect = BattleEffects[effect] as SpriteData;
		if (!start.time) start.time = 0;
		if (!end.time) end.time = start.time + 500;
		start.time += this.timeOffset;
		end.time += this.timeOffset;
		if (!end.scale && end.scale !== 0 && start.scale) end.scale = start.scale;
		if (!end.xscale && end.xscale !== 0 && start.xscale) end.xscale = start.xscale;
		if (!end.yscale && end.yscale !== 0 && start.yscale) end.yscale = start.yscale;
		end = {...start, ...end};

		let startpos = this.pos(start, effect);
		let endpos = this.posT(end, effect, transition, start);

		let $effect = $('<img src="' + effect.url + '" style="display:block;position:absolute" />');
		this.$fx.append($effect);
		$effect = this.$fx.children().last();

		if (start.time) {
			$effect.css({...startpos, opacity: 0});
			$effect.delay(start.time).animate({
				opacity: startpos.opacity,
			}, 1);
		} else {
			$effect.css(startpos);
		}
		$effect.animate(endpos, end.time! - start.time);
		if (after === 'fade') {
			$effect.animate({
				opacity: 0
			}, 100);
		}
		if (after === 'explode') {
			if (end.scale) end.scale *= 3;
			if (end.xscale) end.xscale *= 3;
			if (end.yscale) end.yscale *= 3;
			end.opacity = 0;
			let endendpos = this.pos(end, effect);
			$effect.animate(endendpos, 200);
		}
		this.waitFor($effect);
	}
	backgroundEffect(bg: string, duration: number, opacity = 1, delay = 0) {
		let $effect = $('<div class="background"></div>');
		$effect.css({
			background: bg,
			display: 'block',
			opacity: 0
		});
		this.$bgEffect.append($effect);
		$effect.delay(delay).animate({
			opacity: opacity
		}, 250).delay(duration - 250);
		$effect.animate({
			opacity: 0
		}, 250);
	}

	/**
	 * Converts a PS location (x, y, z, scale, xscale, yscale, opacity)
	 * to a jQuery position (top, left, width, height, opacity) suitable
	 * for passing into `jQuery#css` or `jQuery#animate`.
	 * The display property is passed through if it exists.
	 */
	pos(loc: ScenePos, obj: SpriteData) {
		let left, top, scale, width, height;

		loc = {
			x: 0,
			y: 0,
			z: 0,
			scale: 1,
			opacity: 1,
			...loc,
		};
		if (!loc.xscale && loc.xscale !== 0) loc.xscale = loc.scale;
		if (!loc.yscale && loc.yscale !== 0) loc.yscale = loc.scale;

		left = 210;
		top = 245;
		scale = 1;
		scale = 1.5 - 0.5 * ((loc.z!) / 200);
		if (scale < .1) scale = .1;

		left += (410 - 190) * ((loc.z!) / 200);
		top += (135 - 245) * ((loc.z!) / 200);
		left += Math.floor(loc.x! * scale);
		top -= Math.floor(loc.y! * scale /* - loc.x * scale / 4 */);
		width = Math.floor(obj.w * scale * loc.xscale!);
		height = Math.floor(obj.h * scale * loc.yscale!);
		let hoffset = Math.floor((obj.h - (obj.y || 0) * 2) * scale * loc.yscale!);
		left -= Math.floor(width / 2);
		top -= Math.floor(hoffset / 2);

		let pos = {
			left: left,
			top: top,
			width: width,
			height: height,
			opacity: loc.opacity
		} as JQuery.PlainObject;
		if (loc.display) pos.display = loc.display;
		return pos;
	}
	/**
	 * Converts a PS location to a jQuery transition map (see `pos`)
	 * suitable for passing into `jQuery#animate`.
	 * oldLoc is required for ballistic (jumping) animations.
	 */
	posT(loc: ScenePos, obj: SpriteData, transition?: string, oldLoc?: ScenePos) {
		const pos = this.pos(loc, obj);
		const oldPos = (oldLoc ? this.pos(oldLoc, obj) : null);
		let transitionMap = {
			left: 'linear',
			top: 'linear',
			width: 'linear',
			height: 'linear',
			opacity: 'linear'
		};
		if (transition === 'ballistic') {
			transitionMap.top = (pos.top < oldPos!.top ? 'ballisticUp' : 'ballisticDown');
		}
		if (transition === 'ballisticUnder') {
			transitionMap.top = (pos.top < oldPos!.top ? 'ballisticDown' : 'ballisticUp');
		}
		if (transition === 'ballistic2') {
			transitionMap.top = (pos.top < oldPos!.top ? 'quadUp' : 'quadDown');
		}
		if (transition === 'ballistic2Under') {
			transitionMap.top = (pos.top < oldPos!.top ? 'quadDown' : 'quadUp');
		}
		if (transition === 'swing') {
			transitionMap.left = 'swing';
			transitionMap.top = 'swing';
			transitionMap.width = 'swing';
			transitionMap.height = 'swing';
		}
		if (transition === 'accel') {
			transitionMap.left = 'quadDown';
			transitionMap.top = 'quadDown';
			transitionMap.width = 'quadDown';
			transitionMap.height = 'quadDown';
		}
		if (transition === 'decel') {
			transitionMap.left = 'quadUp';
			transitionMap.top = 'quadUp';
			transitionMap.width = 'quadUp';
			transitionMap.height = 'quadUp';
		}
		return {
			left: [pos.left, transitionMap.left],
			top: [pos.top, transitionMap.top],
			width: [pos.width, transitionMap.width],
			height: [pos.height, transitionMap.height],
			opacity: [pos.opacity, transitionMap.opacity]
		} as JQuery.PlainObject;
	}

	waitFor(elem: JQuery<HTMLElement>) {
		this.activeAnimations = this.activeAnimations.add(elem);
	}

	startAnimations() {
		this.$fx.empty();
		this.activeAnimations = $();
		this.timeOffset = 0;
		this.minDelay = 0;
	}

	finishAnimations() {
		if (this.minDelay || this.timeOffset) {
			this.$delay.delay(Math.max(this.minDelay, this.timeOffset));
			this.activeAnimations = this.activeAnimations.add(this.$delay);
		}
		if (!this.activeAnimations.length) return undefined;
		return this.activeAnimations.promise();
	}

	// Messagebar and log
	/////////////////////////////////////////////////////////////////////

	log(html: string, preempt?: boolean) {
		let willScroll = false;
		if (this.animating) willScroll = (this.$logFrame.scrollTop()! + 60 >= this.$log.height()! + this.$logPreempt.height()! - this.$options.height()! - this.$logFrame.height()!);
		if (preempt) {
			this.$logPreempt.append(html);
		} else {
			this.$log.append(html);
		}
		if (willScroll) {
			this.$logFrame.scrollTop(this.$log.height()! + this.$logPreempt.height()!);
		}
	}
	preemptCatchup() {
		this.$log.append(this.$logPreempt.children().first());
	}
	message(message: string, hiddenMessage?: string) {
		if (!this.messagebarOpen) {
			this.log('<div class="spacer battle-history"></div>');
			if (this.animating) {
				this.$messagebar.empty();
				this.$messagebar.css({
					display: 'block',
					opacity: 0,
					height: 'auto'
				});
				this.$messagebar.animate({
					opacity: 1
				}, this.battle.messageFadeTime / this.acceleration);
			}
		}
		if (this.battle.hardcoreMode && message.slice(0, 8) === '<small>(') {
			hiddenMessage = message + hiddenMessage;
			message = '';
		}
		if (message && this.animating) {
			this.$hiddenMessage.append('<p></p>');
			let $message = this.$hiddenMessage.children().last();
			$message.html(message);
			$message.css({
				display: 'block',
				opacity: 0
			});
			$message.animate({
				height: 'hide'
			}, 1, () => {
				$message.appendTo(this.$messagebar);
				$message.animate({
					height: 'show',
					'padding-bottom': 4,
					opacity: 1
				}, this.battle.messageFadeTime / this.acceleration);
			});
			this.waitFor($message);
		}
		this.messagebarOpen = true;
		this.log('<div class="battle-history">' + message + (hiddenMessage ? hiddenMessage : '') + '</div>');
	}
	closeMessagebar() {
		if (this.messagebarOpen) {
			this.messagebarOpen = false;
			if (this.animating) {
				this.$messagebar.delay(this.battle.messageShownTime / this.acceleration).animate({
					opacity: 0
				}, this.battle.messageFadeTime / this.acceleration);
				this.waitFor(this.$messagebar);
			}
		}
	}

	// General updating
	/////////////////////////////////////////////////////////////////////

	runMoveAnim(moveid: ID, participants: Pokemon[]) {
		if (!this.animating) return;
		BattleMoveAnims[moveid].anim(this, participants.map(p => p.sprite));
	}

	runOtherAnim(moveid: ID, participants: Pokemon[]) {
		if (!this.animating) return;
		BattleOtherAnims[moveid].anim(this, participants.map(p => p.sprite));
	}

	runStatusAnim(moveid: ID, participants: Pokemon[]) {
		if (!this.animating) return;
		BattleStatusAnims[moveid].anim(this, participants.map(p => p.sprite));
	}

	runResidualAnim(moveid: ID, pokemon: Pokemon) {
		if (!this.animating) return;
		BattleMoveAnims[moveid].residualAnim!(this, [pokemon.sprite]);
	}

	runPrepareAnim(moveid: ID, attacker: Pokemon, defender: Pokemon) {
		if (!this.animating) return;
		const moveAnim = BattleMoveAnims[moveid];
		if (!moveAnim.prepareAnim) return;
		moveAnim.prepareAnim(this, [attacker.sprite, defender.sprite]);
		this.message('<small>' + moveAnim.prepareMessage!(attacker, defender) + '</small>');
	}

	updateGen() {
		let gen = this.battle.gen;
		if (Tools.prefs('nopastgens')) gen = 6;
		if (Tools.prefs('bwgfx') && gen > 5) gen = 5;
		this.gen = gen;
		this.activeCount = this.battle.mySide && this.battle.mySide.active.length || 1;

		if (gen <= 1) this.backdropImage = 'fx/bg-gen1.png?';
		else if (gen <= 2) this.backdropImage = 'fx/bg-gen2.png?';
		else if (gen <= 3) this.backdropImage = 'fx/' + BattleBackdropsThree[this.numericId % BattleBackdropsThree.length] + '?';
		else if (gen <= 4) this.backdropImage = 'fx/' + BattleBackdropsFour[this.numericId % BattleBackdropsFour.length];
		else if (gen <= 5) this.backdropImage = 'fx/' + BattleBackdropsFive[this.numericId % BattleBackdropsFive.length];
		else this.backdropImage = 'sprites/gen6bgs/' + BattleBackdrops[this.numericId % BattleBackdrops.length];

		if (this.$bg) {
			this.$bg.css('background-image', 'url(' + Tools.resourcePrefix + '' + this.backdropImage + ')');
		}
	}

	updateSidebar(side: Side) {
		if (!this.animating) return;
		let pokemonhtml = '';
		let noShow = this.battle.hardcoreMode && this.battle.gen < 7;
		let pokemonCount = Math.max(side.pokemon.length, 6);
		for (let i = 0; i < pokemonCount; i++) {
			let poke = side.pokemon[i];
			if (i >= side.totalPokemon && i >= side.pokemon.length) {
				pokemonhtml += '<span class="picon" style="' + Tools.getPokemonIcon('pokeball-none') + '"></span>';
			} else if (noShow && poke && poke.fainted) {
				pokemonhtml += '<span class="picon" style="' + Tools.getPokemonIcon('pokeball-fainted') + '" title="Fainted" aria-label="Fainted"></span>';
			} else if (noShow && poke && poke.status) {
				pokemonhtml += '<span class="picon" style="' + Tools.getPokemonIcon('pokeball-statused') + '" title="Status" aria-label="Status"></span>';
			} else if (noShow) {
				pokemonhtml += '<span class="picon" style="' + Tools.getPokemonIcon('pokeball') + '" title="Non-statused" aria-label="Non-statused"></span>';
			} else if (!poke) {
				pokemonhtml += '<span class="picon" style="' + Tools.getPokemonIcon('pokeball') + '" title="Not revealed" aria-label="Not revealed"></span>';
			} else if (!poke.ident && this.battle.teamPreviewCount && this.battle.teamPreviewCount < side.pokemon.length) {
				pokemonhtml += '<span class="picon" style="' + Tools.getPokemonIcon(poke, !side.n) + ';opacity:0.6" title="' + poke.getFullName(true) + '" aria-label="' + poke.getFullName(true) + '"></span>';
			} else {
				pokemonhtml += '<span class="picon" style="' + Tools.getPokemonIcon(poke, !side.n) + '" title="' + poke.getFullName(true) + '" aria-label="' + poke.getFullName(true) + '"></span>';
			}
			if (i % 3 === 2) pokemonhtml += '</div><div class="teamicons">';
		}
		pokemonhtml = '<div class="teamicons">' + pokemonhtml + '</div>';
		const $sidebar = (side.n ? this.$rightbar : this.$leftbar);
		if (side.name) {
			$sidebar.html('<div class="trainer"><strong>' + Tools.escapeHTML(side.name) + '</strong><div class="trainersprite" style="background-image:url(' + Tools.resolveAvatar(side.spriteid) + ')"></div>' + pokemonhtml + '</div>');
			$sidebar.find('.trainer').css('opacity', 1);
		} else {
			$sidebar.find('.trainer').css('opacity', 0.4);
		}
	}
	updateSidebars() {
		for (const side of this.battle.sides) this.updateSidebar(side);
	}
	updateStatbars() {
		for (const side of this.battle.sides) {
			for (const active of side.active) if (active) {
				active.sprite.updateStatbar(active);
			}
		}
	}

	teamPreviewEnd() {
		for (let siden = 0; siden < 2; siden++) {
			this.$sprites[siden].empty();
			this.battle.sides[siden].updateSprites();
		}
	}
	teamPreview() {
		for (let siden = 0; siden < 2; siden++) {
			let side = this.battle.sides[siden];
			let textBuf = '';
			let buf = '';
			let buf2 = '';
			this.$sprites[siden].empty();

			let ludicoloCount = 0;
			let lombreCount = 0;
			for (let i = 0; i < side.pokemon.length; i++) {
				let pokemon = side.pokemon[i];
				if (pokemon.species === 'Ludicolo') ludicoloCount++;
				if (pokemon.species === 'Lombre') lombreCount++;

				let spriteData = Tools.getSpriteData(pokemon, siden, {
					gen: this.gen,
					noScale: true
				});
				let y = 0;
				let x = 0;
				if (siden) {
					y = 48 + 50 + 3 * (i + 6 - side.pokemon.length);
					x = 48 + 180 + 50 * (i + 6 - side.pokemon.length);
				} else {
					y = 48 + 200 + 3 * i;
					x = 48 + 100 + 50 * i;
				}
				if (textBuf) textBuf += ' / ';
				textBuf += pokemon.species;
				let url = spriteData.url;
				// if (this.paused) url.replace('/xyani', '/xy').replace('.gif', '.png');
				buf += '<img src="' + url + '" width="' + spriteData.w + '" height="' + spriteData.h + '" style="position:absolute;top:' + Math.floor(y - spriteData.h / 2) + 'px;left:' + Math.floor(x - spriteData.w / 2) + 'px" />';
				buf2 += '<div style="position:absolute;top:' + (y + 45) + 'px;left:' + (x - 40) + 'px;width:80px;font-size:10px;text-align:center;color:#FFF;">';
				if (pokemon.gender === 'F') {
					buf2 += '<img src="' + Tools.resourcePrefix + 'fx/gender-f.png" width="7" height="10" alt="F" style="margin-bottom:-1px" /> ';
				} else if (pokemon.gender === 'M') {
					buf2 += '<img src="' + Tools.resourcePrefix + 'fx/gender-m.png" width="7" height="10" alt="M" style="margin-bottom:-1px" /> ';
				}
				if (pokemon.level !== 100) {
					buf2 += '<span style="text-shadow:#000 1px 1px 0,#000 1px -1px 0,#000 -1px 1px 0,#000 -1px -1px 0"><small>L</small>' + pokemon.level + '</span>';
				}
				if (pokemon.item) {
					buf2 += ' <img src="' + Tools.resourcePrefix + 'fx/item.png" width="8" height="10" alt="F" style="margin-bottom:-1px" />';
				}
				buf2 += '</div>';
			}
			side.totalPokemon = side.pokemon.length;
			if (textBuf) {
				this.log('<div class="chat battle-history"><strong>' + Tools.escapeHTML(side.name) + '\'s team:</strong> <em style="color:#445566;display:block;">' + Tools.escapeHTML(textBuf) + '</em></div>');
			}
			this.$sprites[siden].html(buf + buf2);

			if (ludicoloCount >= 2) {
				this.bgmNum = -3;
			} else if (ludicoloCount + lombreCount >= 2) {
				this.bgmNum = -2;
			}
		}
		if (this.bgmNum < 0) {
			this.preloadBgm(this.bgmNum);
			this.soundStart();
		}
		this.wait(1000);
		this.updateSidebars();
	}

	showJoinButtons() {
		if (!this.battle.joinButtons) return;
		if (this.battle.ended || this.battle.rated) return;
		if (!this.battle.p1.name) {
			this.$battle.append('<div class="playbutton1"><button name="joinBattle">Join Battle</button></div>');
		}
		if (!this.battle.p2.name) {
			this.$battle.append('<div class="playbutton2"><button name="joinBattle">Join Battle</button></div>');
		}
	}
	hideJoinButtons() {
		if (!this.battle.joinButtons) return;
		this.$battle.find('.playbutton1, .playbutton2').remove();
	}

	pseudoWeatherLeft(pWeather: WeatherState) {
		let buf = '<br />' + Tools.getMove(pWeather[0]).name;
		if (!pWeather[1] && pWeather[2]) {
			pWeather[1] = pWeather[2];
			pWeather[2] = 0;
		}
		if (this.battle.gen < 7 && this.battle.hardcoreMode) return buf;
		if (pWeather[2]) {
			return buf + ' <small>(' + pWeather[1] + ' or ' + pWeather[2] + ' turns)</small>';
		}
		if (pWeather[1]) {
			return buf + ' <small>(' + pWeather[1] + ' turn' + (pWeather[1] == 1 ? '' : 's') + ')</small>';
		}
		return buf; // weather not found
	}
	sideConditionLeft(cond: [string, number, number, number], siden: number) {
		if (!cond[2] && !cond[3]) return '';
		let buf = '<br />' + (siden ? "Foe's " : "") + Tools.getMove(cond[0]).name;
		if (!cond[2] && cond[3]) {
			cond[2] = cond[3];
			cond[3] = 0;
		}
		if (this.battle.gen < 7 && this.battle.hardcoreMode) return buf;
		if (!cond[3]) {
			return buf + ' <small>(' + cond[2] + ' turn' + (cond[2] == 1 ? '' : 's') + ')</small>';
		}
		return buf + ' <small>(' + cond[2] + ' or ' + cond[3] + ' turns)</small>';
	}
	weatherLeft() {
		if (this.battle.gen < 7 && this.battle.hardcoreMode) return '';
		if (this.battle.weatherMinTimeLeft != 0) {
			return ' <small>(' + this.battle.weatherMinTimeLeft + ' or ' + this.battle.weatherTimeLeft + ' turns)</small>';
		}
		if (this.battle.weatherTimeLeft != 0) {
			return ' <small>(' + this.battle.weatherTimeLeft + ' turn' + (this.battle.weatherTimeLeft == 1 ? '' : 's') + ')</small>';
		}
		return '';
	}
	upkeepWeather() {
		const isIntense = (this.curWeather === 'desolateland' || this.curWeather === 'primordialsea' || this.curWeather === 'deltastream');
		this.$weather.animate({
			opacity: 1.0
		}, 300).animate({
			opacity: isIntense ? 0.9 : 0.5
		}, 300);
	}
	updateWeather(instant?: boolean) {
		if (!this.animating) return;
		let isIntense = false;
		let weatherNameTable = {
			sunnyday: 'Sun',
			desolateland: 'Intense Sun',
			raindance: 'Rain',
			primordialsea: 'Heavy Rain',
			sandstorm: 'Sandstorm',
			hail: 'Hail',
			deltastream: 'Strong Winds'
		} as {[id: string]: string};
		let weather = this.battle.weather;
		let terrain = '' as ID;
		for (const pseudoWeatherData of this.battle.pseudoWeather) {
			let pwid = toId(pseudoWeatherData[0]);
			switch (pwid) {
			case 'electricterrain':
			case 'grassyterrain':
			case 'mistyterrain':
			case 'psychicterrain':
				terrain = pwid;
				break;
			default:
				if (!terrain) terrain = 'pseudo' as ID;
				break;
			}
		}
		if (weather === 'desolateland' || weather === 'primordialsea' || weather === 'deltastream') {
			isIntense = true;
		}

		let weatherhtml = '';
		if (weather && weather in weatherNameTable) {
			weatherhtml += '<br />' + weatherNameTable[weather] + this.weatherLeft();
		}
		for (const pseudoWeather of this.battle.pseudoWeather) {
			weatherhtml += this.pseudoWeatherLeft(pseudoWeather);
		}
		for (const side of this.battle.sides) {
			for (const id in side.sideConditions) {
				weatherhtml += this.sideConditionLeft(side.sideConditions[id], side.n);
			}
		}

		if (instant) {
			this.$weather.html('<em>' + weatherhtml + '</em>');
			if (this.curWeather === weather && this.curTerrain === terrain) return;
			this.$terrain.attr('class', terrain ? 'weather ' + terrain + 'weather' : 'weather');
			this.curTerrain = terrain;
			this.$weather.attr('class', weather ? 'weather ' + weather + 'weather' : 'weather');
			this.$weather.css('opacity', isIntense || !weather ? 0.9 : 0.5);
			this.curWeather = weather;
			return;
		}

		if (weather !== this.curWeather) {
			this.$weather.animate({
				opacity: 0,
			}, this.curWeather ? 300 : 100, () => {
				this.$weather.html('<em>' + weatherhtml + '</em>');
				this.$weather.attr('class', weather ? 'weather ' + weather + 'weather' : 'weather');
				this.$weather.animate({opacity: isIntense || !weather ? 0.9 : 0.5}, 300);
			});
			this.curWeather = weather;
		} else {
			this.$weather.html('<em>' + weatherhtml + '</em>');
		}

		if (terrain !== this.curTerrain) {
			this.$terrain.animate({
				top: 360,
				opacity: 0,
			}, this.curTerrain ? 400 : 1, () => {
				this.$terrain.attr('class', terrain ? 'weather ' + terrain + 'weather' : 'weather');
				this.$terrain.animate({top: 0, opacity: 1}, 400);
			});
			this.curTerrain = terrain;
		}
	}
	resetTurn() {
		if (!this.battle.turn) {
			this.$turn.html('');
			return;
		}
		this.$turn.html('<div class="turn">Turn ' + this.battle.turn + '</div>');
	}
	incrementTurn() {
		if (!this.animating) return;

		const turn = this.battle.turn;
		if (!turn) return;
		const $prevTurn = this.$turn.children();
		const $newTurn = $('<div class="turn">Turn ' + turn + '</div>');
		$newTurn.css({
			opacity: 0,
			left: 160,
		});
		this.$turn.append($newTurn);
		$newTurn.animate({
			opacity: 1,
			left: 110,
		}, 500).animate({
			opacity: .4,
		}, 1500);
		$prevTurn.animate({
			opacity: 0,
			left: 60,
		}, 500, function () {
			$prevTurn.remove();
		});
		if (this.battle.turnsSinceMoved > 2) {
			this.acceleration = (this.battle.messageFadeTime < 150 ? 2 : 1) * Math.min(this.battle.turnsSinceMoved - 1, 3);
		} else {
			this.acceleration = (this.battle.messageFadeTime < 150 ? 2 : 1);
		}
		this.wait(500 / this.acceleration);
	}

	addPokemonSprite(pokemon: Pokemon) {
		const siden = pokemon.side.n;
		const sprite = new PokemonSprite(Tools.getSpriteData(pokemon, siden, {
			gen: this.gen,
		}), {
			x: pokemon.side.x,
			y: pokemon.side.y,
			z: pokemon.side.z,
			opacity: 0,
		}, this, siden);
		if (sprite.$el) this.$sprites[siden].append(sprite.$el);
		return sprite;
	}

	addSideCondition(siden: number, id: ID, instant?: boolean) {
		if (!this.animating) return;
		const side = this.battle.sides[siden];
		switch (id) {
		case 'auroraveil':
			const auroraveil = new Sprite(BattleEffects.auroraveil, {
				display: 'block',
				x: side.x,
				y: side.y,
				z: side.behind(-14),
				xscale: 1,
				yscale: 0,
				opacity: 0.1,
			}, this);
			this.$spritesFront[siden].append(auroraveil.$el!);
			this.sideConditions[siden][id] = [auroraveil];
			auroraveil.anim({
				opacity: 0.7,
				time: instant ? 0 : 400,
			}).anim({
				opacity: 0.3,
				time: instant ? 0 : 300,
			});
			break;
		case 'reflect':
			const reflect = new Sprite(BattleEffects.reflect, {
				display: 'block',
				x: side.x,
				y: side.y,
				z: side.behind(-17),
				xscale: 1,
				yscale: 0,
				opacity: 0.1,
			}, this);
			this.$spritesFront[siden].append(reflect.$el!);
			this.sideConditions[siden][id] = [reflect];
			reflect.anim({
				opacity: 0.7,
				time: instant ? 0 : 400,
			}).anim({
				opacity: 0.3,
				time: instant ? 0 : 300,
			});
			break;
		case 'safeguard':
			const safeguard = new Sprite(BattleEffects.safeguard, {
				display: 'block',
				x: side.x,
				y: side.y,
				z: side.behind(-20),
				xscale: 1,
				yscale: 0,
				opacity: 0.1,
			}, this);
			this.$spritesFront[siden].append(safeguard.$el!);
			this.sideConditions[siden][id] = [safeguard];
			safeguard.anim({
				opacity: 0.7,
				time: instant ? 0 : 400,
			}).anim({
				opacity: 0.3,
				time: instant ? 0 : 300,
			});
			break;
		case 'lightscreen':
			const lightscreen = new Sprite(BattleEffects.lightscreen, {
				display: 'block',
				x: side.x,
				y: side.y,
				z: side.behind(-23),
				xscale: 1,
				yscale: 0,
				opacity: 0.1,
			}, this);
			this.$spritesFront[siden].append(lightscreen.$el!);
			this.sideConditions[siden][id] = [lightscreen];
			lightscreen.anim({
				opacity: 0.7,
				time: instant ? 0 : 400,
			}).anim({
				opacity: 0.3,
				time: instant ? 0 : 300,
			});
			break;
		case 'mist':
			const mist = new Sprite(BattleEffects.mist, {
				display: 'block',
				x: side.x,
				y: side.y,
				z: side.behind(-27),
				xscale: 1,
				yscale: 0,
				opacity: 0.1,
			}, this);
			this.$spritesFront[siden].append(mist.$el!);
			this.sideConditions[siden][id] = [mist];
			mist.anim({
				opacity: 0.7,
				time: instant ? 0 : 400,
			}).anim({
				opacity: 0.3,
				time: instant ? 0 : 300,
			});
			break;
		case 'stealthrock':
			const rock1 = new Sprite(BattleEffects.rock1, {
				display: 'block',
				x: side.leftof(-40),
				y: side.y - 10,
				z: side.z,
				opacity: 0.5,
				scale: 0.2,
			}, this);

			const rock2 = new Sprite(BattleEffects.rock2, {
				display: 'block',
				x: side.leftof(-20),
				y: side.y - 40,
				z: side.z,
				opacity: 0.5,
				scale: 0.2,
			}, this);

			const rock3 = new Sprite(BattleEffects.rock1, {
				display: 'block',
				x: side.leftof(30),
				y: side.y - 20,
				z: side.z,
				opacity: 0.5,
				scale: 0.2,
			}, this);

			const rock4 = new Sprite(BattleEffects.rock2, {
				display: 'block',
				x: side.leftof(10),
				y: side.y - 30,
				z: side.z,
				opacity: 0.5,
				scale: 0.2,
			}, this);

			this.$spritesFront[siden].append(rock1.$el!);
			this.$spritesFront[siden].append(rock2.$el!);
			this.$spritesFront[siden].append(rock3.$el!);
			this.$spritesFront[siden].append(rock4.$el!);
			this.sideConditions[siden][id] = [rock1, rock2, rock3, rock4];
			break;
		case 'spikes':
			let spikeArray = this.sideConditions[siden]['spikes'];
			if (!spikeArray) {
				spikeArray = [];
				this.sideConditions[siden]['spikes'] = spikeArray;
			}
			let levels = this.battle.sides[siden].sideConditions['spikes'][1];
			if (spikeArray.length < 1 && levels >= 1) {
				const spike1 = new Sprite(BattleEffects.caltrop, {
					display: 'block',
					x: side.x - 25,
					y: side.y - 40,
					z: side.z,
					scale: 0.3,
				}, this);
				this.$spritesFront[siden].append(spike1.$el!);
				spikeArray.push(spike1);
			}
			if (spikeArray.length < 2 && levels >= 2) {
				const spike2 = new Sprite(BattleEffects.caltrop, {
					display: 'block',
					x: side.x + 30,
					y: side.y - 45,
					z: side.z,
					scale: .3
				}, this);
				this.$spritesFront[siden].append(spike2.$el!);
				spikeArray.push(spike2);
			}
			if (spikeArray.length < 3 && levels >= 3) {
				const spike3 = new Sprite(BattleEffects.caltrop, {
					display: 'block',
					x: side.x + 50,
					y: side.y - 40,
					z: side.z,
					scale: .3
				}, this);
				this.$spritesFront[siden].append(spike3.$el!);
				spikeArray.push(spike3);
			}
			break;
		case 'toxicspikes':
			let tspikeArray = this.sideConditions[siden]['toxicspikes'];
			if (!tspikeArray) {
				tspikeArray = [];
				this.sideConditions[siden]['toxicspikes'] = tspikeArray;
			}
			let tspikeLevels = this.battle.sides[siden].sideConditions['toxicspikes'][1];
			if (tspikeArray.length < 1 && tspikeLevels >= 1) {
				const tspike1 = new Sprite(BattleEffects.poisoncaltrop, {
					display: 'block',
					x: side.x + 5,
					y: side.y - 40,
					z: side.z,
					scale: 0.3,
				}, this);
				this.$spritesFront[siden].append(tspike1.$el!);
				tspikeArray.push(tspike1);
			}
			if (tspikeArray.length < 2 && tspikeLevels >= 2) {
				const tspike2 = new Sprite(BattleEffects.poisoncaltrop, {
					display: 'block',
					x: side.x - 15,
					y: side.y - 35,
					z: side.z,
					scale: .3
				}, this);
				this.$spritesFront[siden].append(tspike2.$el!);
				tspikeArray.push(tspike2);
			}
			break;
		case 'stickyweb':
			const web = new Sprite(BattleEffects.web, {
				display: 'block',
				x: side.x + 15,
				y: side.y - 35,
				z: side.z,
				opacity: 0.4,
				scale: 0.7,
			}, this);
			this.$spritesFront[siden].append(web.$el!);
			this.sideConditions[siden][id] = [web];
			break;
		}
	}
	removeSideCondition(siden: number, id: ID) {
		if (!this.animating) return;
		if (this.sideConditions[siden][id]) {
			for (const sprite of this.sideConditions[siden][id]) sprite.destroy();
			delete this.sideConditions[siden][id];
		}
	}
	resetSideConditions() {
		for (let siden = 0; siden < this.sideConditions.length; siden++) {
			for (const id in this.sideConditions[siden]) {
				this.removeSideCondition(siden, id as ID);
			}
			for (const id in this.battle.sides[siden].sideConditions) {
				this.addSideCondition(siden, id as ID, true);
			}
		}
	}

	resultAnim(pokemon: Pokemon, result: string, type: 'bad' | 'good' | 'neutral' | StatusName) {
		if (!this.animating) return;
		let $effect = $('<div class="result ' + type + 'result"><strong>' + result + '</strong></div>');
		this.$fx.append($effect);
		$effect.delay(this.timeOffset).css({
			display: 'block',
			opacity: 0,
			top: pokemon.sprite.top - 5,
			left: pokemon.sprite.left - 75
		}).animate({
			opacity: 1
		}, 1);
		$effect.animate({
			opacity: 0,
			top: pokemon.sprite.top - 65
		}, 1000, 'swing');
		this.wait(this.acceleration < 2 ? 350 : 250);
		pokemon.sprite.updateStatbar(pokemon);
		if (this.acceleration < 3) this.waitFor($effect);
	}
	abilityActivateAnim(pokemon: Pokemon, result: string) {
		if (!this.animating) return;
		this.$fx.append('<div class="result abilityresult"><strong>' + result + '</strong></div>');
		let $effect = this.$fx.children().last();
		$effect.delay(this.timeOffset).css({
			display: 'block',
			opacity: 0,
			top: pokemon.sprite.top + 15,
			left: pokemon.sprite.left - 75
		}).animate({
			opacity: 1
		}, 1);
		$effect.delay(800).animate({
			opacity: 0
		}, 400, 'swing');
		this.wait(100);
		pokemon.sprite.updateStatbar(pokemon);
		if (this.acceleration < 3) this.waitFor($effect);
	}
	damageAnim(pokemon: Pokemon, damage: number | string) {
		if (!this.animating) return;
		if (!pokemon.sprite.$statbar) return;
		pokemon.sprite.updateHPText(pokemon);

		let $hp = pokemon.sprite.$statbar.find('div.hp');
		let w = pokemon.hpWidth(150);
		let hpcolor = pokemon.getHPColor();
		let callback;
		if (hpcolor === 'y') callback = function () {
			$hp.addClass('hp-yellow');
		};
		if (hpcolor === 'r') callback = function () {
			$hp.addClass('hp-yellow hp-red');
		};

		this.resultAnim(pokemon, this.battle.hardcoreMode ? 'Damage' : '&minus;' + damage, 'bad');

		$hp.animate({
			width: w,
			'border-right-width': w ? 1 : 0
		}, 350, callback);
	}
	healAnim(pokemon: Pokemon, damage: number | string) {
		if (!this.animating) return;
		if (!pokemon.sprite.$statbar) return;
		pokemon.sprite.updateHPText(pokemon);

		let $hp = pokemon.sprite.$statbar.find('div.hp');
		let w = pokemon.hpWidth(150);
		let hpcolor = pokemon.getHPColor();
		let callback;
		if (hpcolor === 'g') callback = function () {
			$hp.removeClass('hp-yellow hp-red');
		};
		if (hpcolor === 'y') callback = function () {
			$hp.removeClass('hp-red');
		};

		this.resultAnim(pokemon, this.battle.hardcoreMode ? 'Heal' : '+' + damage, 'good');

		$hp.animate({
			width: w,
			'border-right-width': w ? 1 : 0
		}, 350, callback);
	}

	// Misc
	/////////////////////////////////////////////////////////////////////

	preloadImage(url: string) {
		let token = url.replace(/\.(gif|png)$/, '').replace(/\//g, '-');
		if (this.preloadCache[token]) {
			return;
		}
		this.preloadNeeded++;
		this.preloadCache[token] = new Image();
		this.preloadCache[token].onload = () => {
			this.preloadDone++;
		};
		this.preloadCache[token].src = url;
	}
	preloadEffects() {
		for (let i in BattleEffects) {
			if (i === 'alpha' || i === 'omega') continue;
			const url = BattleEffects[i].url;
			if (url) this.preloadImage(url);
		}
		this.preloadImage(Tools.fxPrefix + 'weather-raindance.jpg'); // rain is used often enough to precache
		this.preloadImage(Tools.resourcePrefix + 'sprites/xyani/substitute.gif');
		this.preloadImage(Tools.resourcePrefix + 'sprites/xyani-back/substitute.gif');
		//this.preloadImage(Tools.fxPrefix + 'bg.jpg');
	}
	preloadBgm(bgmNum = 0) {
		if (!bgmNum) bgmNum = this.numericId % 13;
		this.bgmNum = bgmNum;

		let ext = window.nodewebkit ? '.ogg' : '.mp3';
		switch (bgmNum) {
		case -1:
			BattleSound.loadBgm('audio/bw2-homika-dogars' + ext, 1661, 68131);
			this.bgm = 'audio/bw2-homika-dogars' + ext;
			break;
		case -2:
			BattleSound.loadBgm('audio/xd-miror-b' + ext, 9000, 57815);
			this.bgm = 'audio/xd-miror-b' + ext;
			break;
		case -3:
			BattleSound.loadBgm('audio/colosseum-miror-b' + ext, 896, 47462);
			this.bgm = 'audio/colosseum-miror-b' + ext;
			break;
		case 1:
			BattleSound.loadBgm('audio/hgss-kanto-trainer' + ext, 13003, 94656);
			this.bgm = 'audio/hgss-kanto-trainer' + ext;
			break;
		case 2:
			BattleSound.loadBgm('audio/bw-subway-trainer' + ext, 15503, 110984);
			this.bgm = 'audio/bw-subway-trainer' + ext;
			break;
		case 3:
			BattleSound.loadBgm('audio/bw-trainer' + ext, 14629, 110109);
			this.bgm = 'audio/bw-trainer' + ext;
			break;
		case 4:
			BattleSound.loadBgm('audio/bw-rival' + ext, 19180, 57373);
			this.bgm = 'audio/bw-rival' + ext;
			break;
		case 5:
			BattleSound.loadBgm('audio/dpp-trainer' + ext, 13440, 96959);
			this.bgm = 'audio/dpp-trainer' + ext;
			break;
		case 6:
			BattleSound.loadBgm('audio/hgss-johto-trainer' + ext, 23731, 125086);
			this.bgm = 'audio/hgss-johto-trainer' + ext;
			break;
		case 7:
			BattleSound.loadBgm('audio/dpp-rival' + ext, 13888, 66352);
			this.bgm = 'audio/dpp-rival' + ext;
			break;
		case 8:
			BattleSound.loadBgm('audio/bw2-kanto-gym-leader' + ext, 14626, 58986);
			this.bgm = 'audio/bw2-kanto-gym-leader' + ext;
			break;
		case 9:
			BattleSound.loadBgm('audio/bw2-rival' + ext, 7152, 68708);
			this.bgm = 'audio/bw2-rival' + ext;
			break;
		case 10:
			BattleSound.loadBgm('audio/xy-trainer' + ext, 7802, 82469);
			this.bgm = 'audio/xy-trainer' + ext;
			break;
		case 11:
			BattleSound.loadBgm('audio/xy-rival' + ext, 7802, 58634);
			this.bgm = 'audio/xy-rival' + ext;
			break;
		case 12:
			BattleSound.loadBgm('audio/oras-trainer' + ext, 13579, 91548);
			this.bgm = 'audio/oras-trainer' + ext;
			break;
		case 13:
			BattleSound.loadBgm('audio/sm-trainer' + ext, 8323, 89230);
			this.bgm = 'audio/sm-trainer' + ext;
			break;
		case 14:
			BattleSound.loadBgm('audio/sm-rival' + ext, 11389, 62158);
			this.bgm = 'audio/sm-rival' + ext;
			break;
		default:
			BattleSound.loadBgm('audio/oras-rival' + ext, 14303, 69149);
			this.bgm = 'audio/oras-rival' + ext;
			break;
		}
	}
	soundStart() {
		if (!this.bgm) this.preloadBgm();
		BattleSound.playBgm(this.bgm!);
	}
	soundStop() {
		BattleSound.stopBgm();
	}
	soundPause() {
		BattleSound.pauseBgm();
	}
	destroy() {
		if (this.$logFrame) this.$logFrame.empty();
		if (this.$frame) this.$frame.empty();
		this.soundStop();
		this.battle = null!;
	}
}

interface ScenePos {
	x?: number,
	y?: number,
	z?: number,
	scale?: number,
	xscale?: number,
	yscale?: number,
	opacity?: number,
	time?: number,
	display?: string,
}
interface InitScenePos {
	x: number,
	y: number,
	z: number,
	scale?: number,
	xscale?: number,
	yscale?: number,
	opacity?: number,
	time?: number,
	display?: string,
}

class Sprite {
	scene: BattleScene;
	$el: JQuery<HTMLElement> = null!;
	sp: SpriteData;
	x: number;
	y: number;
	z: number;
	constructor(spriteData: SpriteData | null, pos: InitScenePos, scene: BattleScene) {
		this.scene = scene;
		let sp = null;
		if (spriteData) {
			sp = spriteData;
			let rawHTML = sp.rawHTML || '<img src="' + sp.url + '" style="display:none;position:absolute"' + (sp.pixelated ? ' class="pixelated"' : '') + ' />';
			this.$el = $(rawHTML);
		} else {
			sp = {
				w: 0,
				h: 0,
				url: '',
			};
		}
		this.sp = sp;

		this.x = pos.x;
		this.y = pos.y;
		this.z = pos.z;
		if (pos.opacity !== 0 && spriteData) this.$el!.css(scene.pos(pos, sp));

		if (!spriteData) {
			this.delay = function () { return this; };
			this.anim = function () { return this; };
		}
	}

	destroy() {
		if (this.$el) this.$el.remove();
		this.$el = null!;
		this.scene = null!;
	}
	delay(time: number) {
		this.$el!.delay(time);
		return this;
	}
	anim(end: ScenePos, transition?: string) {
		end = {
			x: this.x,
			y: this.y,
			z: this.z,
			scale: 1,
			opacity: 1,
			time: 500,
			...end,
		};
		if (end.time === 0) {
			this.$el!.css(this.scene.pos(end, this.sp));
			return this;
		}
		this.$el!.animate(this.scene.posT(end, this.sp, transition, this), end.time!);
		return this;
	}
}

class PokemonSprite extends Sprite {
	siden: number;
	forme = '';
	cryurl: string | undefined = undefined;

	subsp: SpriteData | null = null;
	$sub: JQuery<HTMLElement> | null = null;
	isSubActive = false;

	$statbar: JQuery<HTMLElement> | null = null;
	isBackSprite: boolean;
	isMissedPokemon = false;
	/**
	 * If the pokemon is transformed, sprite.sp will be the transformed
	 * SpriteData and sprite.oldsp will hold the original form's SpriteData
	 */
	oldsp: SpriteData | null = null;

	statbarLeft = 0;
	statbarTop = 0;
	left = 0;
	top = 0;

	effects = {} as {[id: string]: Sprite[]};

	constructor(spriteData: SpriteData | null, pos: InitScenePos, scene: BattleScene, siden: number) {
		super(spriteData, pos, scene);
		this.siden = siden;
		this.cryurl = this.sp.cryurl;
		this.isBackSprite = !this.siden;
	}
	destroy() {
		if (this.$el) this.$el.remove();
		this.$el = null!;
		if (this.$statbar) this.$statbar.remove();
		this.$statbar = null;
		if (this.$sub) this.$sub.remove();
		this.$sub = null;
		this.scene = null!;
	}

	delay(time: number) {
		this.$el.delay(time);
		if (this.$sub) this.$sub.delay(time);
		return this;
	}
	anim(end: ScenePos, transition?: string) {
		end = {
			x: this.x,
			y: this.y,
			z: this.z,
			scale: 1,
			opacity: 1,
			time: 500,
			...end,
		};
		const [$el, sp] = (this.isSubActive ? [this.$sub!, this.subsp!] : [this.$el, this.sp]);
		$el.animate(this.scene.posT(end, sp, transition, this), end.time!);
		return this;
	}

	behindx(offset: number) {
		return this.x + (this.isBackSprite ? -1 : 1) * offset;
	}
	behindy(offset: number) {
		return this.y + (this.isBackSprite ? 1 : -1) * offset;
	}
	leftof(offset: number) {
		return this.x + (this.isBackSprite ? -1 : 1) * offset;
	}
	behind(offset: number) {
		return this.z + (this.isBackSprite ? -1 : 1) * offset;
	}

	removeTransform() {
		if (!this.scene.animating) return;
		if (!this.oldsp) return;
		let sp = this.oldsp;
		this.cryurl = sp.cryurl;
		this.sp = sp;
		this.oldsp = null;

		const $el = this.isSubActive ? this.$sub! : this.$el;
		$el.attr('src', sp.url!);
		$el.css(this.scene.pos({
			x: this.x,
			y: this.y,
			z: (this.isSubActive ? this.behind(30) : this.z),
			opacity: (this.$sub ? .3 : 1),
		}, sp));
	}
	animSub(instant?: boolean, noAnim?: boolean) {
		if (!this.scene.animating) return;
		if (this.$sub) return;
		const subsp = Tools.getSpriteData('substitute', this.siden, {
			gen: this.scene.gen
		});
		this.subsp = subsp;
		this.$sub = $('<img src="' + subsp.url + '" style="display:block;opacity:0;position:absolute"' + (subsp.pixelated ? ' class="pixelated"' : '') + ' />');
		this.scene.$spritesFront[this.siden].append(this.$sub);
		this.isSubActive = true;
		if (instant) {
			if (!noAnim) this.animReset();
			return;
		}
		this.$el.animate(this.scene.pos({
			x: this.x,
			y: this.y,
			z: this.behind(30),
			opacity: 0.3,
		}, this.sp), 500);
		this.$sub.css(this.scene.pos({
			x: this.x,
			y: this.y + 50,
			z: this.z,
			opacity: 0
		}, subsp));
		this.$sub.animate(this.scene.pos({
			x: this.x,
			y: this.y,
			z: this.z
		}, subsp), 500);
		this.scene.waitFor(this.$sub);
	}
	animSubFade(instant?: boolean) {
		if (!this.$sub || !this.scene.animating) return;
		this.isSubActive = false;
		if (instant) {
			this.$sub.remove();
			this.$sub = null;
			this.animReset();
			return;
		}
		if (this.scene.timeOffset) {
			this.$el.delay(this.scene.timeOffset);
			this.$sub.delay(this.scene.timeOffset);
		}
		this.$sub.animate(this.scene.pos({
			x: this.x,
			y: this.y - 50,
			z: this.z,
			opacity: 0
		}, this.subsp!), 500);

		this.$sub = null;
		this.anim({time: 500});
		if (this.scene.animating) this.scene.waitFor(this.$el);
	}
	beforeMove() {
		if (!this.scene.animating) return false;
		if (!this.isSubActive) return false;
		this.isSubActive = false;
		this.anim({time: 300});
		this.$sub!.animate(this.scene.pos({
			x: this.leftof(-50),
			y: this.y,
			z: this.z,
			opacity: 0.5
		}, this.subsp!), 300);
		for (const side of this.scene.battle.sides) {
			for (const active of side.active) {
				if (active && active.sprite !== this) {
					active.sprite.delay(300);
				}
			}
		}
		this.scene.wait(300);
		this.scene.waitFor(this.$el);

		return true;
	}
	afterMove() {
		if (!this.scene.animating) return false;
		if (!this.$sub || this.isSubActive) return false;
		this.isSubActive = true;
		this.$sub.delay(300);
		this.$el.add(this.$sub).promise().done(() => {
			if (!this.$sub || !this.$el) return;
			this.$el.animate(this.scene.pos({
				x: this.x,
				y: this.y,
				z: this.behind(30),
				opacity: 0.3,
			}, this.sp), 300);
			this.anim({time: 300});
		});
		return false;
	}
	removeSub() {
		if (!this.$sub) return;
		this.isSubActive = false;
		if (!this.scene.animating) {
			this.$sub.remove();
		} else {
			const $sub = this.$sub;
			$sub.animate({
				opacity: 0
			}, () => {
				$sub.remove();
			});
		}
		this.$sub = null;
	}
	reset(pokemon: Pokemon) {
		this.clearEffects();

		if (pokemon.volatiles.formechange) {
			this.oldsp = this.sp;
			this.sp = Tools.getSpriteData(pokemon, this.isBackSprite ? 0 : 1, {
				gen: this.scene.gen,
			});
		}

		// I can rant for ages about how jQuery sucks, necessitating this function
		// The short version is: after calling elem.finish() on an animating
		// element, there appear to be a grand total of zero ways to hide it
		// afterwards. I've tried `elem.css('display', 'none')`, `elem.hide()`,
		// `elem.hide(1)`, `elem.hide(1000)`, `elem.css('opacity', 0)`,
		// `elem.animate({opacity: 0}, 1000)`.
		// They literally all do nothing, and the element retains
		// a style attribute containing `display: inline-block` and `opacity: 1`
		// Only forcibly removing the element from the DOM actually makes it
		// disappear, so that's what we do.
		if (this.$el) {
			this.$el.stop(true, false);
			const $newEl = $('<img src="' + this.sp.url + '" style="display:none;position:absolute"' + (this.sp.pixelated ? ' class="pixelated"' : '') + ' />');
			this.$el.replaceWith($newEl);
			this.$el = $newEl;
		}

		if (!pokemon.isActive()) {
			if (this.$statbar) {
				this.$statbar.remove();
				this.$statbar = null;
			}
			return;
		}

		this.recalculatePos(pokemon.slot);
		this.resetStatbar(pokemon);
		this.$el.css(this.scene.pos({
			display: 'block',
			x: this.x,
			y: this.y,
			z: this.z,
		}, this.sp));

		for (const id in pokemon.volatiles) this.addEffect(id as ID, true);
		for (const id in pokemon.turnstatuses) this.addEffect(id as ID, true);
		for (const id in pokemon.movestatuses) this.addEffect(id as ID, true);
	}
	animReset() {
		if (!this.scene.animating) return;
		if (this.$sub) {
			this.isSubActive = true;
			this.$el.stop(true, false);
			this.$sub.stop(true, false);
			this.$el.css(this.scene.pos({
				x: this.x,
				y: this.y,
				z: this.behind(30),
				opacity: .3
			}, this.sp));
			this.$sub.css(this.scene.pos({
				x: this.x,
				y: this.y,
				z: this.z
			}, this.subsp!));
		} else {
			this.$el.stop(true, false);
			this.$el.css(this.scene.pos({
				x: this.x,
				y: this.y,
				z: this.z
			}, this.sp));
		}
	}
	recalculatePos(slot: number) {
		let moreActive = this.scene.activeCount - 1;
		let statbarOffset = 0;
		if (this.scene.gen <= 4 && moreActive) {
			this.x = (slot - 0.52) * (this.isBackSprite ? -1 : 1) * -55;
			this.y = (this.isBackSprite ? -1 : 1) + 1;
			if (!this.isBackSprite) statbarOffset = 30 * slot;
			if (this.isBackSprite) statbarOffset = -28 * slot;
		} else {
			switch (moreActive) {
			case 0:
				this.x = 0;
				break;
			case 1:
				if (this.sp.pixelated) {
					this.x = (slot * -100 + 18) * (this.isBackSprite ? -1 : 1);
				} else {
					this.x = (slot * -75 + 18) * (this.isBackSprite ? -1 : 1);
				}
				break;
			case 2:
				this.x = (slot * -70 + 20) * (this.isBackSprite ? -1 : 1);
				break;
			}
			this.y = (slot * 10) * (this.isBackSprite ? -1 : 1);
			if (!this.isBackSprite) statbarOffset = 17 * slot;
			if (!this.isBackSprite && !moreActive && this.sp.pixelated) statbarOffset = 15;
			if (this.isBackSprite) statbarOffset = -7 * slot;
			if (!this.isBackSprite && moreActive == 2) statbarOffset = 14 * slot - 10;
		}
		if (this.scene.gen <= 2) {
			statbarOffset += this.isBackSprite ? 1 : 20;
		} else if (this.scene.gen <= 3) {
			statbarOffset += this.isBackSprite ? 5 : 30;
		} else {
			statbarOffset += this.isBackSprite ? 20 : 30;
		}

		let pos = this.scene.pos({
			x: this.x,
			y: this.y,
			z: this.z
		}, {
			w: 0,
			h: 96
		});
		pos.top += 40;

		this.left = pos.left;
		this.top = pos.top;
		this.statbarLeft = pos.left - 80;
		this.statbarTop = pos.top - 73 - statbarOffset;

		if (moreActive) {
			// make sure element is in the right z-order
			if (!slot && this.isBackSprite || slot && !this.isBackSprite) {
				this.$el.prependTo(this.$el.parent());
			} else {
				this.$el.appendTo(this.$el.parent());
			}
		}
	}
	animSummon(pokemon: Pokemon, slot: number, instant?: boolean) {
		if (!this.scene.animating) return;
		this.scene.$sprites[this.siden].append(this.$el);
		this.recalculatePos(slot);

		// 'z-index': (this.isBackSprite ? 1+slot : 4-slot),
		if (instant) {
			this.$el.css('display', 'block');
			this.animReset();
			this.resetStatbar(pokemon);
			if (pokemon.hasVolatile('substitute' as ID)) this.animSub(true);
			return;
		}
		if (this.cryurl) {
			BattleSound.playEffect(this.cryurl);
		}
		this.$el.css(this.scene.pos({
			display: 'block',
			x: this.x,
			y: this.y - 10,
			z: this.z,
			scale: 0,
			opacity: 0,
		}, this.sp));
		this.scene.showEffect('pokeball', {
			opacity: 0,
			x: this.x,
			y: this.y + 30,
			z: this.behind(50),
			scale: .7
		}, {
			opacity: 1,
			x: this.x,
			y: this.y - 10,
			z: this.z,
			time: 300 / this.scene.acceleration
		}, 'ballistic2', 'fade');
		if (this.scene.gen <= 4) {
			this.delay(this.scene.timeOffset + 300 / this.scene.acceleration).anim({
				x: this.x,
				y: this.y,
				z: this.z,
				time: 400 / this.scene.acceleration,
			});
		} else {
			this.delay(this.scene.timeOffset + 300 / this.scene.acceleration).anim({
				x: this.x,
				y: this.y + 30,
				z: this.z,
				time: 400 / this.scene.acceleration,
			}).anim({
				x: this.x,
				y: this.y,
				z: this.z,
				time: 300 / this.scene.acceleration,
			}, 'accel');
		}
		if (this.sp.shiny && this.scene.acceleration < 2) BattleOtherAnims.shiny.anim(this.scene, [this]);
		this.scene.waitFor(this.$el);

		if (pokemon.hasVolatile('substitute' as ID)) {
			this.animSub(true, true);
			this.$sub!.css(this.scene.pos({
				x: this.x,
				y: this.y,
				z: this.z
			}, this.subsp!));
			this.$el.animate(this.scene.pos({
				x: this.x,
				y: this.y,
				z: this.behind(30),
				opacity: 0.3,
			}, this.sp), 300);
		}

		this.resetStatbar(pokemon, true);
		this.scene.updateSidebar(pokemon.side);
		this.$statbar!.css({
			display: 'block',
			left: this.statbarLeft,
			top: this.statbarTop + 20,
			opacity: 0,
		});
		this.$statbar!.delay(300 / this.scene.acceleration).animate({
			top: this.statbarTop,
			opacity: 1,
		}, 400 / this.scene.acceleration);

		this.dogarsCheck(pokemon);
	}
	animDragIn(pokemon: Pokemon, slot: number) {
		if (!this.scene.animating) return;
		this.scene.$sprites[this.siden].append(this.$el);
		this.recalculatePos(slot);

		// 'z-index': (this.isBackSprite ? 1+slot : 4-slot),
		this.$el.css(this.scene.pos({
			display: 'block',
			x: this.leftof(-100),
			y: this.y,
			z: this.z,
			opacity: 0
		}, this.sp));
		this.delay(300).anim({
			x: this.x,
			y: this.y,
			z: this.z,
			time: 400,
		}, 'decel');
		if (!!this.scene.animating && this.sp.shiny) BattleOtherAnims.shiny.anim(this.scene, [this]);
		this.scene.waitFor(this.$el);
		this.scene.timeOffset = 700;

		this.resetStatbar(pokemon, true);
		this.scene.updateSidebar(pokemon.side);
		this.$statbar!.css({
			display: 'block',
			left: this.statbarLeft + (this.siden ? -100 : 100),
			top: this.statbarTop,
			opacity: 0,
		});
		this.$statbar!.delay(300).animate({
			left: this.statbarLeft,
			opacity: 1,
		}, 400);

		this.dogarsCheck(pokemon);
	}
	animDragOut(pokemon: Pokemon) {
		if (!this.scene.animating) return this.animUnsummon(pokemon, true);
		if (this.$sub) {
			this.isSubActive = false;
			const $sub = this.$sub;
			$sub.animate(this.scene.pos({
				x: this.leftof(100),
				y: this.y,
				z: this.z,
				opacity: 0,
				time: 400,
			}, this.subsp!), () => {
				$sub.remove();
			});
			this.$sub = null;
		}
		this.anim({
			x: this.leftof(100),
			y: this.y,
			z: this.z,
			opacity: 0,
			time: 400,
		}, 'accel');

		this.updateStatbar(pokemon, true);
		let $statbar = this.$statbar;
		if ($statbar) {
			this.$statbar = null;
			$statbar.animate({
				left: this.statbarLeft - (this.siden ? -100 : 100),
				opacity: 0
			}, 300 / this.scene.acceleration, () => {
				$statbar!.remove();
			});
		}
	}
	animUnsummon(pokemon: Pokemon, instant?: boolean) {
		this.removeSub();
		if (!this.scene.animating || instant) {
			this.$el.hide();
			if (this.$statbar) {
				this.$statbar.remove();
				this.$statbar = null;
			}
			return;
		}
		if (this.scene.gen <= 4) {
			this.anim({
				x: this.x,
				y: this.y - 25,
				z: this.z,
				scale: 0,
				opacity: 0,
				time: 400 / this.scene.acceleration,
			});
		} else {
			this.anim({
				x: this.x,
				y: this.y - 40,
				z: this.z,
				scale: 0,
				opacity: 0,
				time: 400 / this.scene.acceleration,
			});
		}
		this.scene.showEffect('pokeball', {
			opacity: 1,
			x: this.x,
			y: this.y - 40,
			z: this.z,
			scale: .7,
			time: 300 / this.scene.acceleration,
		}, {
			opacity: 0,
			x: this.x,
			y: this.y,
			z: this.behind(50),
			time: 700 / this.scene.acceleration,
		}, 'ballistic2');
		if (this.scene.acceleration < 3) this.scene.wait(600 / this.scene.acceleration);

		this.updateStatbar(pokemon, true);
		let $statbar = this.$statbar;
		if ($statbar) {
			this.$statbar = null;
			$statbar.animate({
				left: this.statbarLeft + (this.siden ? 50 : -50),
				opacity: 0
			}, 300 / this.scene.acceleration, () => {
				$statbar!.remove();
			});
		}
	}
	animFaint(pokemon: Pokemon) {
		this.removeSub();
		if (!this.scene.animating) {
			this.$el.remove();
			if (this.$statbar) {
				this.$statbar.remove();
				this.$statbar = null;
			}
			return;
		}
		this.updateStatbar(pokemon, false, true);
		this.scene.updateSidebar(pokemon.side);
		if (this.cryurl) {
			BattleSound.playEffect(this.cryurl);
		}
		this.anim({
			y: this.y - 80,
			opacity: 0,
		}, 'accel');
		this.scene.waitFor(this.$el);
		this.$el.promise().done(() => {
			this.$el.remove();
		});

		let $statbar = this.$statbar;
		if ($statbar) {
			this.$statbar = null;
			$statbar.animate({
				opacity: 0,
			}, 300, () => {
				$statbar!.remove();
			});
		}
	}
	animTransform(pokemon: Pokemon, isCustomAnim?: boolean, isPermanent?: boolean) {
		if (!this.scene.animating && !isPermanent) return;
		let sp = Tools.getSpriteData(pokemon, this.isBackSprite ? 0 : 1, {
			gen: this.scene.gen,
		});
		let oldsp = this.sp;
		if (isPermanent) {
			this.oldsp = null;
		} else if (!this.oldsp) {
			this.oldsp = oldsp;
		}
		this.sp = sp;
		this.cryurl = sp.cryurl;

		if (!this.scene.animating) return;
		let speciesid = toId(pokemon.getSpecies());
		let doCry = false;
		const scene = this.scene;
		if (isCustomAnim) {
			if (speciesid === 'kyogreprimal') {
				BattleOtherAnims.primalalpha.anim(scene, [this]);
				doCry = true;
			} else if (speciesid === 'groudonprimal') {
				BattleOtherAnims.primalomega.anim(scene, [this]);
				doCry = true;
			} else if (speciesid === 'necrozmaultra') {
				BattleOtherAnims.ultraburst.anim(scene, [this]);
				doCry = true;
			} else if (speciesid === 'zygardecomplete') {
				BattleOtherAnims.powerconstruct.anim(scene, [this]);
			} else if (speciesid === 'wishiwashischool' || speciesid === 'greninjaash') {
				BattleOtherAnims.schoolingin.anim(scene, [this]);
			} else if (speciesid === 'wishiwashi') {
				BattleOtherAnims.schoolingout.anim(scene, [this]);
			} else if (speciesid === 'mimikyubusted' || speciesid === 'mimikyubustedtotem') {
				// standard animation
			} else {
				BattleOtherAnims.megaevo.anim(scene, [this]);
				doCry = true;
			}
		}
		// Constructing here gives us 300ms extra time to preload the new sprite
		let $newEl = $('<img src="' + sp.url + '" style="display:block;opacity:0;position:absolute"' + (sp.pixelated ? ' class="pixelated"' : '') + ' />');
		$newEl.css(this.scene.pos({
			x: this.x,
			y: this.y,
			z: this.z,
			yscale: 0,
			xscale: 0,
			opacity: 0,
		}, sp));
		this.$el.animate(this.scene.pos({
			x: this.x,
			y: this.y,
			z: this.z,
			yscale: 0,
			xscale: 0,
			opacity: 0.3,
		}, oldsp), 300, () => {
			if (this.cryurl && doCry) {
				BattleSound.playEffect(this.cryurl);
			}
			this.$el.replaceWith($newEl);
			this.$el = $newEl;
			this.$el.animate(scene.pos({
				x: this.x,
				y: this.y,
				z: this.z,
				opacity: 1
			}, sp), 300);
		});
		this.scene.wait(500);

		if (isPermanent) {
			this.scene.updateSidebar(pokemon.side);
			this.resetStatbar(pokemon);
		} else {
			this.updateStatbar(pokemon);
		}
	}

	pokeEffect(id: ID) {
		if (id === 'protect' || id === 'magiccoat') {
			this.effects[id][0].anim({
				scale: 1.2,
				opacity: 1,
				time: 100,
			}).anim({
				opacity: .4,
				time: 300,
			});
		}
	}
	addEffect(id: ID, instant?: boolean) {
		if (id in this.effects) {
			this.pokeEffect(id);
			return;
		}
		if (id === 'substitute') {
			this.animSub(instant);
		} else if (id === 'leechseed') {
			const pos1 = {
				display: 'block',
				x: this.x - 30,
				y: this.y - 40,
				z: this.z,
				scale: .2,
				opacity: .6,
			};
			const pos2 = {
				display: 'block',
				x: this.x + 40,
				y: this.y - 35,
				z: this.z,
				scale: .2,
				opacity: .6,
			};
			const pos3 = {
				display: 'block',
				x: this.x + 20,
				y: this.y - 25,
				z: this.z,
				scale: .2,
				opacity: .6,
			};

			const leechseed1 = new Sprite(BattleEffects.energyball, pos1, this.scene);
			const leechseed2 = new Sprite(BattleEffects.energyball, pos2, this.scene);
			const leechseed3 = new Sprite(BattleEffects.energyball, pos3, this.scene);
			this.scene.$spritesFront[this.siden].append(leechseed1.$el!);
			this.scene.$spritesFront[this.siden].append(leechseed2.$el!);
			this.scene.$spritesFront[this.siden].append(leechseed3.$el!);
			this.effects['leechseed'] = [leechseed1, leechseed2, leechseed3];
		} else if (id === 'protect' || id === 'magiccoat') {
			const protect = new Sprite(BattleEffects.protect, {
				display: 'block',
				x: this.x,
				y: this.y,
				z: this.behind(-15),
				xscale: 1,
				yscale: 0,
				opacity: .1,
			}, this.scene);
			this.scene.$spritesFront[this.siden].append(protect.$el!);
			this.effects[id] = [protect];
			protect.anim({
				opacity: .9,
				time: instant ? 0 : 400,
			}).anim({
				opacity: .4,
				time: instant ? 0 : 300,
			});
		}
	}

	removeEffect(id: ID, instant?: boolean) {
		if (id === 'formechange') this.removeTransform();
		if (id === 'substitute') this.animSubFade(instant);
		if (this.effects[id]) {
			for (const sprite of this.effects[id]) sprite.destroy();
			delete this.effects[id];
		}
	}
	clearEffects() {
		for (const id in this.effects) this.removeEffect(id as ID, true);
		this.animSubFade(true);
		this.removeTransform();
	}

	dogarsCheck(pokemon: Pokemon) {
		if (pokemon.side.n === 1) return;

		if (pokemon.species === 'Koffing' && pokemon.name.match(/dogars/i)) {
			if (this.scene.bgmNum !== -1) {
				this.scene.preloadBgm(-1);
				this.scene.soundStart();
			}
		} else if (this.scene.bgmNum === -1) {
			this.scene.bgmNum = 0;
			this.scene.preloadBgm();
			this.scene.soundStart();
		}
	}

	// Statbar
	/////////////////////////////////////////////////////////////////////

	getStatbarHTML(pokemon: Pokemon) {
		let buf = '<div class="statbar' + (this.siden ? ' lstatbar' : ' rstatbar') + '" style="display: none">';
		buf += '<strong>' + (this.siden && (this.scene.battle.ignoreOpponent || this.scene.battle.ignoreNicks) ? pokemon.species : Tools.escapeHTML(pokemon.name));
		let gender = pokemon.gender;
		if (gender) buf += ' <img src="' + Tools.resourcePrefix + 'fx/gender-' + gender.toLowerCase() + '.png" alt="' + gender + '" />';
		buf += (pokemon.level === 100 ? '' : ' <small>L' + pokemon.level + '</small>');

		let symbol = '';
		if (pokemon.species.indexOf('-Mega') >= 0) symbol = 'mega';
		else if (pokemon.species === 'Kyogre-Primal') symbol = 'alpha';
		else if (pokemon.species === 'Groudon-Primal') symbol = 'omega';
		if (symbol) buf += ' <img src="' + Tools.resourcePrefix + 'sprites/misc/' + symbol + '.png" alt="' + symbol + '" style="vertical-align:text-bottom;" />';

		buf += '</strong><div class="hpbar"><div class="hptext"></div><div class="hptextborder"></div><div class="prevhp"><div class="hp"></div></div><div class="status"></div>';
		buf += '</div>';
		return buf;
	}

	resetStatbar(pokemon: Pokemon, startHidden?: boolean) {
		if (this.$statbar) {
			this.$statbar.remove();
			this.$statbar = null;
		}
		this.updateStatbar(pokemon, true);
		if (!startHidden && this.$statbar) {
			this.$statbar!.css({
				display: 'block',
				left: this.statbarLeft,
				top: this.statbarTop,
				opacity: 1,
			});
		}
	}

	updateStatbar(pokemon: Pokemon, updatePrevhp?: boolean, updateHp?: boolean) {
		if (!this.scene.animating) return;
		if (!pokemon.isActive()) {
			if (this.$statbar) this.$statbar.hide();
			return;
		}
		if (!this.$statbar) {
			this.$statbar = $(this.getStatbarHTML(pokemon));
			this.scene.$stat.append(this.$statbar);
			updatePrevhp = true;
		}
		let hpcolor;
		if (updatePrevhp || updateHp) {
			hpcolor = pokemon.getHPColor();
			let w = pokemon.hpWidth(150);
			let $hp = this.$statbar.find('.hp');
			$hp.css({
				width: w,
				'border-right-width': (w ? 1 : 0)
			});
			if (hpcolor === 'g') $hp.removeClass('hp-yellow hp-red');
			else if (hpcolor === 'y') $hp.removeClass('hp-red').addClass('hp-yellow');
			else $hp.addClass('hp-yellow hp-red');
			this.updateHPText(pokemon);
		}
		if (updatePrevhp) {
			let $prevhp = this.$statbar.find('.prevhp');
			$prevhp.css('width', pokemon.hpWidth(150) + 1);
			if (hpcolor === 'g') $prevhp.removeClass('prevhp-yellow prevhp-red');
			else if (hpcolor === 'y') $prevhp.removeClass('prevhp-red').addClass('prevhp-yellow');
			else $prevhp.addClass('prevhp-yellow prevhp-red');
		}
		let status = '';
		if (pokemon.status === 'brn') {
			status += '<span class="brn">BRN</span> ';
		} else if (pokemon.status === 'psn') {
			status += '<span class="psn">PSN</span> ';
		} else if (pokemon.status === 'tox') {
			status += '<span class="psn">TOX</span> ';
		} else if (pokemon.status === 'slp') {
			status += '<span class="slp">SLP</span> ';
		} else if (pokemon.status === 'par') {
			status += '<span class="par">PAR</span> ';
		} else if (pokemon.status === 'frz') {
			status += '<span class="frz">FRZ</span> ';
		}
		if (pokemon.volatiles.typechange && pokemon.volatiles.typechange[1]) {
			let types = pokemon.volatiles.typechange[1].split('/');
			status += '<img src="' + Tools.resourcePrefix + 'sprites/types/' + encodeURIComponent(types[0]) + '.png" alt="' + types[0] + '" /> ';
			if (types[1]) {
				status += '<img src="' + Tools.resourcePrefix + 'sprites/types/' + encodeURIComponent(types[1]) + '.png" alt="' + types[1] + '" /> ';
			}
		}
		if (pokemon.volatiles.typeadd) {
			const type = pokemon.volatiles.typeadd[1];
			status += '+<img src="' + Tools.resourcePrefix + 'sprites/types/' + type + '.png" alt="' + type + '" /> ';
		}
		for (const stat in pokemon.boosts) {
			if (pokemon.boosts[stat]) {
				status += '<span class="' + pokemon.getBoostType(stat as BoostStatName) + '">' + pokemon.getBoost(stat as BoostStatName) + '</span> ';
			}
		}
		let statusTable = {
			throatchop: '<span class="bad">Throat&nbsp;Chop</span> ',
			confusion: '<span class="bad">Confused</span> ',
			healblock: '<span class="bad">Heal&nbsp;Block</span> ',
			yawn: '<span class="bad">Drowsy</span> ',
			flashfire: '<span class="good">Flash&nbsp;Fire</span> ',
			imprison: '<span class="good">Imprisoning&nbsp;foe</span> ',
			formechange: '',
			typechange: '',
			typeadd: '',
			autotomize: '<span class="neutral">Lightened</span> ',
			miracleeye: '<span class="bad">Miracle&nbsp;Eye</span> ',
			foresight: '<span class="bad">Foresight</span> ',
			telekinesis: '<span class="neutral">Telekinesis</span> ',
			transform: '<span class="neutral">Transformed</span> ',
			powertrick: '<span class="neutral">Power&nbsp;Trick</span> ',
			curse: '<span class="bad">Curse</span> ',
			nightmare: '<span class="bad">Nightmare</span> ',
			attract: '<span class="bad">Attract</span> ',
			torment: '<span class="bad">Torment</span> ',
			taunt: '<span class="bad">Taunt</span> ',
			disable: '<span class="bad">Disable</span> ',
			embargo: '<span class="bad">Embargo</span> ',
			ingrain: '<span class="good">Ingrain</span> ',
			aquaring: '<span class="good">Aqua&nbsp;Ring</span> ',
			stockpile1: '<span class="good">Stockpile</span> ',
			stockpile2: '<span class="good">Stockpile&times;2</span> ',
			stockpile3: '<span class="good">Stockpile&times;3</span> ',
			perish0: '<span class="bad">Perish&nbsp;now</span>',
			perish1: '<span class="bad">Perish&nbsp;next&nbsp;turn</span> ',
			perish2: '<span class="bad">Perish&nbsp;in&nbsp;2</span> ',
			perish3: '<span class="bad">Perish&nbsp;in&nbsp;3</span> ',
			airballoon: '<span class="good">Balloon</span> ',
			leechseed: '<span class="bad">Leech&nbsp;Seed</span> ',
			encore: '<span class="bad">Encore</span> ',
			mustrecharge: '<span class="bad">Must&nbsp;recharge</span> ',
			bide: '<span class="good">Bide</span> ',
			magnetrise: '<span class="good">Magnet&nbsp;Rise</span> ',
			smackdown: '<span class="bad">Smack&nbsp;Down</span> ',
			focusenergy: '<span class="good">Focus&nbsp;Energy</span> ',
			slowstart: '<span class="bad">Slow&nbsp;Start</span> ',
			doomdesire: '',
			futuresight: '',
			mimic: '<span class="good">Mimic</span> ',
			watersport: '<span class="good">Water&nbsp;Sport</span> ',
			mudsport: '<span class="good">Mud&nbsp;Sport</span> ',
			substitute: '',
			// sub graphics are handled elsewhere, see Battle.Sprite.animSub()
			uproar: '<span class="neutral">Uproar</span>',
			rage: '<span class="neutral">Rage</span>',
			roost: '<span class="neutral">Landed</span>',
			protect: '<span class="good">Protect</span>',
			quickguard: '<span class="good">Quick&nbsp;Guard</span>',
			wideguard: '<span class="good">Wide&nbsp;Guard</span>',
			craftyshield: '<span class="good">Crafty&nbsp;Shield</span>',
			matblock: '<span class="good">Mat&nbsp;Block</span>',
			helpinghand: '<span class="good">Helping&nbsp;Hand</span>',
			magiccoat: '<span class="good">Magic&nbsp;Coat</span>',
			destinybond: '<span class="good">Destiny&nbsp;Bond</span>',
			snatch: '<span class="good">Snatch</span>',
			grudge: '<span class="good">Grudge</span>',
			endure: '<span class="good">Endure</span>',
			focuspunch: '<span class="neutral">Focusing</span>',
			shelltrap: '<span class="neutral">Trap&nbsp;set</span>',
			powder: '<span class="bad">Powder</span>',
			electrify: '<span class="bad">Electrify</span>',
			ragepowder: '<span class="good">Rage&nbsp;Powder</span>',
			followme: '<span class="good">Follow&nbsp;Me</span>',
			instruct: '<span class="neutral">Instruct</span>',
			beakblast: '<span class="neutral">Beak&nbsp;Blast</span>',
			laserfocus: '<span class="good">Laser&nbsp;Focus</span>',
			spotlight: '<span class="neutral">Spotlight</span>',
			itemremoved: '',
			// Gen 1
			lightscreen: '<span class="good">Light&nbsp;Screen</span>',
			reflect: '<span class="good">Reflect</span>'
		} as {[id: string]: string};
		for (let i in pokemon.volatiles) {
			if (typeof statusTable[i] === 'undefined') status += '<span class="neutral">[[' + i + ']]</span>';
			else status += statusTable[i];
		}
		for (let i in pokemon.turnstatuses) {
			if (typeof statusTable[i] === 'undefined') status += '<span class="neutral">[[' + i + ']]</span>';
			else status += statusTable[i];
		}
		for (let i in pokemon.movestatuses) {
			if (typeof statusTable[i] === 'undefined') status += '<span class="neutral">[[' + i + ']]</span>';
			else status += statusTable[i];
		}
		let statusbar = this.$statbar.find('.status');
		statusbar.html(status);
	}

	updateHPText(pokemon: Pokemon) {
		if (!this.$statbar) return;
		let $hptext = this.$statbar.find('.hptext');
		let $hptextborder = this.$statbar.find('.hptextborder');
		if (pokemon.maxhp === 48 || this.scene.battle.hardcoreMode && pokemon.maxhp === 100) {
			$hptext.hide();
			$hptextborder.hide();
		} else if (this.scene.battle.hardcoreMode) {
			$hptext.html(pokemon.hp + '/');
			$hptext.show();
			$hptextborder.show();
		} else {
			$hptext.html(pokemon.hpWidth(100) + '%');
			$hptext.show();
			$hptextborder.show();
		}
	}
}

// par: -webkit-filter:  sepia(100%) hue-rotate(373deg) saturate(592%);
//      -webkit-filter:  sepia(100%) hue-rotate(22deg) saturate(820%) brightness(29%);
// psn: -webkit-filter:  sepia(100%) hue-rotate(618deg) saturate(285%);
// brn: -webkit-filter:  sepia(100%) hue-rotate(311deg) saturate(469%);
// slp: -webkit-filter:  grayscale(100%);
// frz: -webkit-filter:  sepia(100%) hue-rotate(154deg) saturate(759%) brightness(23%);

// @ts-ignore
Object.assign($.easing, {
	ballisticUp(x: number, t: number, b: number, c: number, d: number) {
		return -3 * x * x + 4 * x;
	},
	ballisticDown(x: number, t: number, b: number, c: number, d: number) {
		x = 1 - x;
		return 1 - (-3 * x * x + 4 * x);
	},
	quadUp(x: number, t: number, b: number, c: number, d: number) {
		x = 1 - x;
		return 1 - (x * x);
	},
	quadDown(x: number, t: number, b: number, c: number, d: number) {
		return x * x;
	}
});

const BattleSound = new class {
	effectCache = {} as {[url: string]: any};

	// bgm
	bgmCache = {} as {[url: string]: any};
	bgm: any = null!;

	// misc
	soundPlaceholder = {
		play: function () { return this; },
		pause: function () { return this; },
		stop: function () { return this; },
		resume: function () { return this; },
		setVolume: function () { return this; },
		onposition: function () { return this; }
	}

	// options
	effectVolume = 50;
	bgmVolume = 50;
	muted = false;

	loadEffect(url: string) {
		if (this.effectCache[url] && this.effectCache[url] !== this.soundPlaceholder) {
			return this.effectCache[url];
		}
		try {
			this.effectCache[url] = soundManager.createSound({
				id: url,
				url: Tools.resourcePrefix + url,
				volume: this.effectVolume
			});
		} catch (e) {}
		if (!this.effectCache[url]) {
			this.effectCache[url] = this.soundPlaceholder;
		}
		return this.effectCache[url];
	}
	playEffect(url: string) {
		if (!this.muted) this.loadEffect(url).setVolume(this.effectVolume).play();
	}

	loadBgm(url: string, loopstart?: number, loopend?: number) {
		if (this.bgmCache[url]) {
			if (this.bgmCache[url] !== this.soundPlaceholder || loopstart === undefined) {
				return this.bgmCache[url];
			}
		}
		try {
			this.bgmCache[url] = soundManager.createSound({
				id: url,
				url: Tools.resourcePrefix + url,
				volume: this.bgmVolume
			});
		} catch (e) {}
		if (!this.bgmCache[url]) {
			// couldn't load
			// suppress crash
			return (this.bgmCache[url] = this.soundPlaceholder);
		}
		this.bgmCache[url].onposition(loopend, function (this: any, evP: any) {
			this.setPosition(this.position - (loopend! - loopstart!));
		});
		return this.bgmCache[url];
	}
	playBgm(url: string, loopstart?: number, loopstop?: number) {
		if (this.bgm === this.loadBgm(url, loopstart, loopstop)) {
			if (!this.bgm.paused && this.bgm.playState) {
				return;
			}
		} else {
			this.stopBgm();
		}
		try {
			this.bgm = this.loadBgm(url, loopstart, loopstop).setVolume(this.bgmVolume);
			if (!this.muted) {
				if (this.bgm.paused) {
					this.bgm.resume();
				} else {
					this.bgm.play();
				}
			}
		} catch (e) {}
	}
	pauseBgm() {
		if (this.bgm) {
			this.bgm.pause();
		}
	}
	stopBgm() {
		if (this.bgm) {
			this.bgm.stop();
			this.bgm = null;
		}
	}

	// setting
	setMute(muted: boolean) {
		muted = !!muted;
		if (this.muted == muted) return;
		this.muted = muted;
		if (muted) {
			if (this.bgm) this.bgm.pause();
		} else {
			if (this.bgm) this.bgm.play();
		}
	}

	loudnessPercentToAmplitudePercent(loudnessPercent: number) {
		// 10 dB is perceived as approximately twice as loud
		let decibels = 10 * Math.log(loudnessPercent / 100) / Math.log(2);
		return Math.pow(10, decibels / 20) * 100;
	}
	setBgmVolume(bgmVolume: number) {
		this.bgmVolume = this.loudnessPercentToAmplitudePercent(bgmVolume);
		if (this.bgm) {
			try {
				this.bgm.setVolume(this.bgmVolume);
			} catch (e) {}
		}
	}
	setEffectVolume(effectVolume: number) {
		this.effectVolume = this.loudnessPercentToAmplitudePercent(effectVolume);
	}
};

interface AnimData {
	anim(scene: BattleScene, args: PokemonSprite[]): void;
	prepareAnim?(scene: BattleScene, args: PokemonSprite[]): void;
	residualAnim?(scene: BattleScene, args: PokemonSprite[]): void;
	prepareMessage?(pokemon: Pokemon, target: Pokemon): string;
	multihit?: boolean;
}
type AnimTable = {[k: string]: AnimData};

var BattleEffects: {[k: string]: SpriteData} = {
	wisp: {
		url: 'wisp.png',
		w: 100, h: 100
	},
	poisonwisp: {
		url: 'poisonwisp.png',
		w: 100, h: 100
	},
	waterwisp: {
		url: 'waterwisp.png',
		w: 100, h: 100
	},
	mudwisp: {
		url: 'mudwisp.png',
		w: 100, h: 100
	},
	blackwisp: {
		url: 'blackwisp.png',
		w: 100, h: 100
	},
	fireball: {
		url: 'fireball.png',
		w: 64, h: 64
	},
	bluefireball: {
		url: 'bluefireball.png',
		w: 64, h: 64
	},
	icicle: {
		url: 'icicle.png', // http://opengameart.org/content/icicle-spell
		w: 80, h: 60
	},
	lightning: {
		url: 'lightning.png', // by Pokemon Showdown user SailorCosmos
		w: 41, h: 229
	},
	rocks: {
		url: 'rocks.png', // Pokemon Online - Gilad
		w: 100, h: 100
	},
	rock1: {
		url: 'rock1.png', // Pokemon Online - Gilad
		w: 64, h: 80
	},
	rock2: {
		url: 'rock2.png', // Pokemon Online - Gilad
		w: 66, h: 72
	},
	rock3: {
		url: 'rock3.png', // by Pokemon Showdown user SailorCosmos
		w: 66, h: 72
	},
	leaf1: {
		url: 'leaf1.png',
		w: 32, h: 26
	},
	leaf2: {
		url: 'leaf2.png',
		w: 40, h: 26
	},
	bone: {
		url: 'bone.png',
		w: 29, h: 29
	},
	caltrop: {
		url: 'caltrop.png', // by Pokemon Showdown user SailorCosmos
		w: 80, h: 80
	},
	poisoncaltrop: {
		url: 'poisoncaltrop.png', // by Pokemon Showdown user SailorCosmos
		w: 80, h: 80
	},
	shadowball: {
		url: 'shadowball.png',
		w: 100, h: 100
	},
	energyball: {
		url: 'energyball.png',
		w: 100, h: 100
	},
	electroball: {
		url: 'electroball.png',
		w: 100, h: 100
	},
	mistball: {
		url: 'mistball.png',
		w: 100, h: 100
	},
	iceball: {
		url: 'iceball.png',
		w: 100, h: 100
	},
	flareball: {
		url: 'flareball.png',
		w: 100, h: 100
	},
	pokeball: {
		url: 'pokeball.png',
		w: 24, h: 24
	},
	fist: {
		url: 'fist.png', // by Pokemon Showdown user SailorCosmos
		w: 55, h: 49
	},
	fist1: {
		url: 'fist1.png',
		w: 49, h: 55
	},
	foot: {
		url: 'foot.png', // by Pokemon Showdown user SailorCosmos
		w: 50, h: 75
	},
	topbite: {
		url: 'topbite.png',
		w: 108, h: 64
	},
	bottombite: {
		url: 'bottombite.png',
		w: 108, h: 64
	},
	web: {
		url: 'web.png', // by Pokemon Showdown user SailorCosmos
		w: 120, h: 122
	},
	leftclaw: {
		url: 'leftclaw.png',
		w: 44, h: 60
	},
	rightclaw: {
		url: 'rightclaw.png',
		w: 44, h: 60
	},
	leftslash: {
		url: 'leftslash.png', // by Pokemon Showdown user Modeling Clay
		w: 57, h: 56
	},
	rightslash: {
		url: 'rightslash.png', // by Pokemon Showdown user Modeling Clay
		w: 57, h: 56
	},
	leftchop: {
		url: 'leftchop.png', // by Pokemon Showdown user SailorCosmos
		w: 100, h: 130
	},
	rightchop: {
		url: 'rightchop.png', // by Pokemon Showdown user SailorCosmos
		w: 100, h: 130
	},
	angry: {
		url: 'angry.png', // by Pokemon Showdown user SailorCosmos
		w: 30, h: 30
	},
	heart: {
		url: 'heart.png', // by Pokemon Showdown user SailorCosmos
		w: 30, h: 30
	},
	pointer: {
		url: 'pointer.png', // by Pokemon Showdown user SailorCosmos
		w: 100, h: 100
	},
	sword: {
		url: 'sword.png', // by Pokemon Showdown user SailorCosmos
		w: 48, h: 100
	},
	impact: {
		url: 'impact.png', // by Pokemon Showdown user SailorCosmos
		w: 127, h: 119
	},
	stare: {
		url: 'stare.png',
		w: 100, h: 35
	},
	shine: {
		url: 'shine.png', // by Smogon user Jajoken
		w: 127, h: 119
	},
	feather: {
		url: 'feather.png', // Ripped from http://www.clker.com/clipart-black-and-white-feather.html
		w: 100, h: 38
	},
	shell: {
		url: 'shell.png', // by Smogon user Jajoken
		w: 100, h: 91.5
	},
	petal: {
		url: 'petal.png', // by Smogon user Jajoken
		w: 60, h: 60
	},
	gear: {
		url: 'gear.png', // by Smogon user Jajoken
		w: 100, h: 100
	},
	alpha: {
		url: 'alpha.png', // Ripped from Pokemon Global Link
		w: 80, h: 80
	},
	omega: {
		url: 'omega.png', // Ripped from Pokemon Global Link
		w: 80, h: 80
	},
	rainbow: {
		url: 'rainbow.png',
		w: 128, h: 128
	},
	zsymbol: {
		url: 'z-symbol.png', // From http://froggybutt.deviantart.com/art/Pokemon-Z-Move-symbol-633125033
		w: 150, h: 100
	},
	ultra: {
		url: 'ultra.png', // by Pokemon Showdown user Modeling Clay
		w: 113, h: 165
	},
	hitmark: {
		url: 'hitmarker.png', // by Pokemon Showdown user Ridaz
		w: 100, h: 100
	},
	protect: {
		rawHTML: '<div class="turnstatus-protect" style="display:none;position:absolute" />',
		w: 100, h: 70,
	},
	auroraveil: {
		rawHTML: '<div class="sidecondition-auroraveil" style="display:none;position:absolute" />',
		w: 100, h: 50,
	},
	reflect: {
		rawHTML: '<div class="sidecondition-reflect" style="display:none;position:absolute" />',
		w: 100, h: 50,
	},
	safeguard: {
		rawHTML: '<div class="sidecondition-safeguard" style="display:none;position:absolute" />',
		w: 100, h: 50,
	},
	lightscreen: {
		rawHTML: '<div class="sidecondition-lightscreen" style="display:none;position:absolute" />',
		w: 100, h: 50,
	},
	mist: {
		rawHTML: '<div class="sidecondition-mist" style="display:none;position:absolute" />',
		w: 100, h: 50,
	},
};
(function () {
	if (!window.Tools || !Tools.resourcePrefix) return;
	for (var i in BattleEffects) {
		if (!BattleEffects[i].url) continue;
		BattleEffects[i].url = Tools.fxPrefix + BattleEffects[i].url;
	}
})();
var BattleBackdropsThree = [
	'bg-gen3.png',
	'bg-gen3-cave.png',
	'bg-gen3-ocean.png',
	'bg-gen3-sand.png',
	'bg-gen3-forest.png',
	'bg-gen3-arena.png'
];
var BattleBackdropsFour = [
	'bg-gen4.png',
	'bg-gen4-cave.png',
	'bg-gen4-snow.png',
	'bg-gen4-indoors.png',
	'bg-gen4-water.png'
];
var BattleBackdropsFive = [
	'bg-beach.png',
	'bg-beachshore.png',
	'bg-desert.png',
	'bg-meadow.png',
	'bg-thunderplains.png',
	'bg-city.png',
	'bg-earthycave.png',
	'bg-mountain.png',
	'bg-volcanocave.png',
	'bg-dampcave.png',
	'bg-forest.png',
	'bg-river.png',
	'bg-deepsea.png',
	'bg-icecave.png',
	'bg-route.png'
];
var BattleBackdrops = [
	'bg-aquacordetown.jpg',
	'bg-beach.jpg',
	'bg-city.jpg',
	'bg-dampcave.jpg',
	'bg-darkbeach.jpg',
	'bg-darkcity.jpg',
	'bg-darkmeadow.jpg',
	'bg-deepsea.jpg',
	'bg-desert.jpg',
	'bg-earthycave.jpg',
	'bg-elite4drake.jpg',
	'bg-forest.jpg',
	'bg-icecave.jpg',
	'bg-leaderwallace.jpg',
	'bg-library.jpg',
	'bg-meadow.jpg',
	'bg-orasdesert.jpg',
	'bg-orassea.jpg',
	'bg-skypillar.jpg'
];

const BattleOtherAnims: AnimTable = {
	hitmark: {
		anim(scene, [attacker]) {
			scene.showEffect('hitmark', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 1
			}, {
				opacity: 0.5,
				time: 250
			}, 'linear', 'fade');
		}
	},
	attack: {
		anim(scene, [attacker, defender]) {
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.1,
				opacity: 1
			}, {
				x: defender.x,
				y: defender.y,
				z: defender.behind(40),
				scale: 1,
				opacity: 0.5
			}, 'linear');
		}
	},
	contactattack: {
		anim(scene, [attacker, defender]) {
			attacker.anim({
				x: defender.x,
				y: defender.y + 80,
				z: defender.behind(-30),
				time: 400
			}, 'ballistic');
			attacker.anim({
				x: defender.x,
				y: defender.y + 5,
				z: defender.z,
				time: 100
			});
			attacker.anim({
				time: 500
			}, 'ballistic2Back');
			defender.delay(450);
			defender.anim({
				z: defender.behind(20),
				time: 100
			}, 'swing');
			defender.anim({
				time: 300
			}, 'swing');
			scene.wait(500);
		}
	},
	xattack: {
		anim(scene, [attacker, defender]) {
			attacker.anim({
				x: defender.leftof(-30),
				y: defender.y + 80,
				z: defender.behind(-30),
				time: 400
			}, 'ballistic');
			attacker.anim({
				x: defender.leftof(30),
				y: defender.y + 5,
				z: defender.z,
				time: 100
			});
			attacker.anim({
				x: defender.leftof(30),
				y: defender.y + 80,
				z: defender.behind(-30),
				time: 200
			}, 'ballisticUp');
			attacker.anim({
				x: defender.leftof(-30),
				y: defender.y + 5,
				z: defender.z,
				time: 100
			});
			attacker.anim({
				time: 500
			}, 'ballistic2Back');
			defender.delay(450);
			defender.anim({
				z: defender.behind(20),
				time: 100
			}, 'swing');
			defender.anim({
				time: 200
			}, 'swing');
			defender.anim({
				z: defender.behind(20),
				time: 100
			}, 'swing');
			defender.anim({
				time: 300
			}, 'swing');
		}
	},
	slashattack: {
		anim(scene, [attacker, defender]) {
			attacker.anim({
				x: defender.x,
				y: defender.y + 80,
				z: defender.behind(-30),
				time: 400
			}, 'ballistic');
			attacker.anim({
				x: defender.x,
				y: defender.y + 5,
				z: defender.z,
				time: 100
			});
			attacker.anim({
				time: 500
			}, 'ballistic2Back');
			defender.delay(450);
			defender.anim({
				z: defender.behind(20),
				time: 100
			}, 'swing');
			defender.anim({
				time: 300
			}, 'swing');

			scene.showEffect('rightslash', {
				x: defender.x,
				y: defender.y,
				z: defender.z,
				scale: 1,
				opacity: 1,
				time: 500
			}, {
				scale: 3,
				opacity: 0,
				time: 800
			}, 'linear', 'fade');
		}
	},
	clawattack: {
		anim(scene, [attacker, defender]) {
			attacker.anim({
				x: defender.leftof(-30),
				y: defender.y + 80,
				z: defender.behind(-30),
				time: 400
			}, 'ballistic');
			attacker.anim({
				x: defender.leftof(30),
				y: defender.y + 5,
				z: defender.z,
				time: 100
			});
			attacker.anim({
				x: defender.leftof(30),
				y: defender.y + 80,
				z: defender.behind(-30),
				time: 200
			}, 'ballisticUp');
			attacker.anim({
				x: defender.leftof(-30),
				y: defender.y + 5,
				z: defender.z,
				time: 100
			});
			attacker.anim({
				time: 500
			}, 'ballistic2Back');
			defender.delay(450);
			defender.anim({
				z: defender.behind(20),
				time: 100
			}, 'swing');
			defender.anim({
				time: 200
			}, 'swing');
			defender.anim({
				z: defender.behind(20),
				time: 100
			}, 'swing');
			defender.anim({
				time: 300
			}, 'swing');

			scene.showEffect('leftclaw', {
				x: defender.x - 20,
				y: defender.y + 20,
				z: defender.z,
				scale: 0,
				opacity: 1,
				time: 400
			}, {
				x: defender.x - 20,
				y: defender.y + 20,
				z: defender.z,
				scale: 3,
				opacity: 0,
				time: 700
			}, 'linear', 'fade');
			scene.showEffect('leftclaw', {
				x: defender.x - 20,
				y: defender.y - 20,
				z: defender.z,
				scale: 0,
				opacity: 1,
				time: 400
			}, {
				x: defender.x - 20,
				y: defender.y - 20,
				z: defender.z,
				scale: 3,
				opacity: 0,
				time: 700
			}, 'linear', 'fade');
			scene.showEffect('rightclaw', {
				x: defender.x + 20,
				y: defender.y + 20,
				z: defender.z,
				scale: 0,
				opacity: 1,
				time: 700
			}, {
				x: defender.x + 20,
				y: defender.y + 20,
				z: defender.z,
				scale: 3,
				opacity: 0,
				time: 1000
			}, 'linear', 'fade');
			scene.showEffect('rightclaw', {
				x: defender.x + 20,
				y: defender.y - 20,
				z: defender.z,
				scale: 0,
				opacity: 1,
				time: 700
			}, {
				x: defender.x + 20,
				y: defender.y - 20,
				z: defender.z,
				scale: 3,
				opacity: 0,
				time: 1000
			}, 'linear', 'fade');
		}
	},
	punchattack: {
		anim(scene, [attacker, defender]) {
			scene.showEffect('wisp', {
				x: defender.x,
				y: defender.y,
				z: defender.z,
				scale: 0,
				opacity: 1,
				time: 400
			}, {
				x: defender.leftof(-20),
				y: defender.y,
				z: defender.behind(20),
				scale: 3,
				opacity: 0,
				time: 700
			}, 'linear');
			scene.showEffect('wisp', {
				x: defender.x,
				y: defender.y,
				z: defender.z,
				scale: 0,
				opacity: 1,
				time: 500
			}, {
				x: defender.leftof(-20),
				y: defender.y,
				z: defender.behind(20),
				scale: 3,
				opacity: 0,
				time: 800
			}, 'linear');
			scene.showEffect('fist', {
				x: defender.x,
				y: defender.y,
				z: defender.z,
				scale: 1,
				opacity: 1,
				time: 400
			}, {
				x: defender.leftof(-20),
				y: defender.y,
				z: defender.behind(20),
				scale: 2,
				opacity: 0,
				time: 800
			}, 'linear');
			attacker.anim({
				x: defender.leftof(20),
				y: defender.y,
				z: defender.behind(-20),
				time: 400
			}, 'ballistic2Under');
			attacker.anim({
				x: defender.x,
				y: defender.y,
				z: defender.z,
				time: 50
			});
			attacker.anim({
				time: 500
			}, 'ballistic2');
			defender.delay(425);
			defender.anim({
				x: defender.leftof(-15),
				y: defender.y,
				z: defender.behind(15),
				time: 50
			}, 'swing');
			defender.anim({
				time: 300
			}, 'swing');
		}
	},
	bite: {
		anim(scene, [attacker, defender]) {
			scene.showEffect('topbite', {
				x: defender.x,
				y: defender.y + 50,
				z: defender.z,
				scale: 0.5,
				opacity: 0,
				time: 370
			}, {
				x: defender.x,
				y: defender.y + 10,
				z: defender.z,
				scale: 0.5,
				opacity: 1,
				time: 500
			}, 'linear', 'fade');
			scene.showEffect('bottombite', {
				x: defender.x,
				y: defender.y - 50,
				z: defender.z,
				scale: 0.5,
				opacity: 0,
				time: 370
			}, {
				x: defender.x,
				y: defender.y - 10,
				z: defender.z,
				scale: 0.5,
				opacity: 1,
				time: 500
			}, 'linear', 'fade');
		}
	},
	kick: {
		anim(scene, [attacker, defender]) {
			scene.showEffect('foot', {
				x: defender.x,
				y: defender.y,
				z: defender.z,
				scale: 1,
				opacity: 1,
				time: 400
			}, {
				x: defender.x,
				y: defender.y - 20,
				z: defender.behind(15),
				scale: 2,
				opacity: 0,
				time: 800
			}, 'linear');
		}
	},
	fastattack: {
		anim(scene, [attacker, defender]) {
			scene.showEffect('wisp', {
				x: defender.x,
				y: defender.y,
				z: defender.z,
				scale: 0,
				opacity: 0.5,
				time: 260
			}, {
				scale: 2,
				opacity: 0,
				time: 560
			}, 'linear');
			scene.showEffect('wisp', {
				x: defender.x,
				y: defender.y,
				z: defender.z,
				scale: 0,
				opacity: 0.5,
				time: 310
			}, {
				scale: 2,
				opacity: 0,
				time: 610
			}, 'linear');
			scene.showEffect(attacker.sp, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				opacity: 0.3,
				time: 50
			}, {
				x: defender.x,
				y: defender.y,
				z: defender.behind(70),
				time: 350
			}, 'accel', 'fade');
			scene.showEffect(attacker.sp, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				opacity: 0.3,
				time: 100
			}, {
				x: defender.x,
				y: defender.y,
				z: defender.behind(70),
				time: 400
			}, 'accel', 'fade');
			attacker.anim({
				x: defender.x,
				y: defender.y,
				z: defender.behind(70),
				time: 300,
				opacity: 0.5
			}, 'accel');
			attacker.anim({
				x: defender.x,
				y: defender.x,
				z: defender.behind(100),
				opacity: 0,
				time: 100
			}, 'linear');
			attacker.anim({
				x: attacker.x,
				y: attacker.y,
				z: attacker.behind(70),
				opacity: 0,
				time: 1
			}, 'linear');
			attacker.anim({
				opacity: 1,
				time: 500
			}, 'decel');
			defender.delay(260);
			defender.anim({
				z: defender.behind(30),
				time: 100
			}, 'swing');
			defender.anim({
				time: 300
			}, 'swing');
		}
	},
	sneakattack: {
		anim(scene, [attacker, defender]) {
			attacker.anim({
				x: attacker.leftof(-20),
				y: attacker.y,
				z: attacker.behind(-20),
				opacity: 0,
				time: 200
			}, 'linear');
			attacker.anim({
				x: defender.x,
				y: defender.y,
				z: defender.behind(-120),
				opacity: 0,
				time: 1
			}, 'linear');
			attacker.anim({
				x: defender.x,
				y: defender.y,
				z: defender.behind(40),
				opacity: 1,
				time: 250
			}, 'linear');
			attacker.anim({
				x: defender.x,
				y: defender.y,
				z: defender.behind(-5),
				opacity: 0,
				time: 300
			}, 'linear');
			attacker.anim({
				opacity: 0,
				time: 1
			}, 'linear');
			attacker.anim({
				time: 300,
				opacity: 1
			}, 'linear');
			defender.delay(330);
			defender.anim({
				z: defender.behind(20),
				time: 100
			}, 'swing');
			defender.anim({
				time: 300
			}, 'swing');
		}
	},
	spinattack: {
		anim(scene, [attacker, defender]) {
			attacker.anim({
				x: defender.x,
				y: defender.y + 60,
				z: defender.behind(-30),
				time: 400
			}, 'ballistic2');
			attacker.anim({
				x: defender.x,
				y: defender.y + 5,
				z: defender.z,
				time: 100
			});
			attacker.anim({
				time: 500
			}, 'ballistic2');
			defender.delay(450);
			defender.anim({
				z: defender.behind(20),
				time: 100
			}, 'swing');
			defender.anim({
				time: 300
			}, 'swing');
			scene.wait(500);
		}
	},
	bound: {
		anim(scene, [attacker]) {
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y + 15,
				z: attacker.z,
				scale: 0.7,
				xscale: 2,
				opacity: 0.3,
				time: 0
			}, {
				scale: 0.4,
				xscale: 1,
				opacity: 0.1,
				time: 500
			}, 'decel', 'fade');
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y - 5,
				z: attacker.z,
				scale: 0.7,
				xscale: 2,
				opacity: 0.3,
				time: 50
			}, {
				scale: 0.4,
				xscale: 1,
				opacity: 0.1,
				time: 550
			}, 'decel', 'fade');
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y - 20,
				z: attacker.z,
				scale: 0.7,
				xscale: 2,
				opacity: 0.3,
				time: 100
			}, {
				scale: 0.4,
				xscale: 1,
				opacity: 0.1,
				time: 600
			}, 'decel', 'fade');
			attacker.anim({
				y: attacker.y + 15,
				z: attacker.behind(10),
				yscale: 1.3,
				time: 200
			}, 'swing');
			attacker.anim({
				time: 200
			}, 'swing');
			attacker.delay(25);
			attacker.anim({
				x: attacker.leftof(-10),
				y: attacker.y + 15,
				z: attacker.behind(5),
				yscale: 1.3,
				time: 200
			}, 'swing');
			attacker.anim({
				time: 200
			}, 'swing');
		}
	},
	selfstatus: {
		anim(scene, [attacker]) {
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 2,
				opacity: 0.2,
				time: 0
			}, {
				scale: 0,
				opacity: 1,
				time: 300
			}, 'linear');
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 2,
				opacity: 0.2,
				time: 200
			}, {
				scale: 0,
				opacity: 1,
				time: 500
			}, 'linear');
		}
	},
	lightstatus: {
		anim(scene, [attacker]) {
			scene.showEffect('electroball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 2,
				opacity: 0.1,
				time: 0
			}, {
				scale: 0,
				opacity: 0.5,
				time: 600
			}, 'linear');
		}
	},
	chargestatus: {
		anim(scene, [attacker]) {
			scene.showEffect('electroball', {
				x: attacker.x - 60,
				y: attacker.y + 40,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 0
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 300
			}, 'linear', 'fade');
			scene.showEffect('electroball', {
				x: attacker.x + 60,
				y: attacker.y - 5,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 300
			}, 'linear', 'fade');
			scene.showEffect('electroball', {
				x: attacker.x - 30,
				y: attacker.y + 60,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 400
			}, 'linear', 'fade');
			scene.showEffect('electroball', {
				x: attacker.x + 20,
				y: attacker.y - 50,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 400
			}, 'linear', 'fade');
			scene.showEffect('electroball', {
				x: attacker.x - 70,
				y: attacker.y - 50,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 200
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 500
			}, 'linear', 'fade');
		}
	},
	heal: {
		anim(scene, [attacker]) {
			scene.showEffect('iceball', {
				x: attacker.x + 30,
				y: attacker.y + 5,
				z: attacker.z,
				scale: 0.1,
				opacity: 0.7,
				time: 200
			}, {
				x: attacker.x + 40,
				y: attacker.y + 10,
				opacity: 0,
				time: 600
			}, 'accel');
			scene.showEffect('iceball', {
				x: attacker.x - 30,
				y: attacker.y - 10,
				z: attacker.z,
				scale: 0.1,
				opacity: 0.7,
				time: 300
			}, {
				x: attacker.x - 40,
				y: attacker.y - 20,
				opacity: 0,
				time: 700
			}, 'accel');
			scene.showEffect('iceball', {
				x: attacker.x + 15,
				y: attacker.y + 10,
				z: attacker.z,
				scale: 0.1,
				opacity: 0.7,
				time: 400
			}, {
				x: attacker.x + 25,
				y: attacker.y + 20,
				opacity: 0,
				time: 800
			}, 'accel');
			scene.showEffect('iceball', {
				x: attacker.x - 15,
				y: attacker.y - 30,
				z: attacker.z,
				scale: 0.1,
				opacity: 0.7,
				time: 500
			}, {
				x: attacker.x - 25,
				y: attacker.y - 40,
				opacity: 0,
				time: 900
			}, 'accel');
		}
	},
	shiny: {
		anim(scene, [attacker]) {
			scene.backgroundEffect('#000000', 800, 0.3, 100);
			scene.showEffect('shine', {
				x: attacker.x + 5,
				y: attacker.y + 20,
				z: attacker.z,
				scale: 0.1,
				opacity: 0.7,
				time: 450
			}, {
				y: attacker.y + 35,
				opacity: 0,
				time: 675
			}, 'decel');
			scene.showEffect('shine', {
				x: attacker.x + 15,
				y: attacker.y + 20,
				z: attacker.z,
				scale: 0.2,
				opacity: 0.7,
				time: 475
			}, {
				x: attacker.x + 25,
				y: attacker.y + 30,
				opacity: 0,
				time: 700
			}, 'decel');
			scene.showEffect('shine', {
				x: attacker.x - 15,
				y: attacker.y + 20,
				z: attacker.z,
				scale: 0.2,
				opacity: 0.7,
				time: 500
			}, {
				x: attacker.x - 25,
				y: attacker.y + 30,
				opacity: 0,
				time: 725
			}, 'decel');
			scene.showEffect('shine', {
				x: attacker.x - 20,
				y: attacker.y + 5,
				z: attacker.z,
				scale: 0.2,
				opacity: 0.7,
				time: 550
			}, {
				x: attacker.x - 30,
				y: attacker.y - 5,
				opacity: 0,
				time: 775
			}, 'decel');
			scene.showEffect('shine', {
				x: attacker.x + 15,
				y: attacker.y + 10,
				z: attacker.z,
				scale: 0.2,
				opacity: 0.7,
				time: 650
			}, {
				x: attacker.x + 35,
				y: attacker.y - 5,
				opacity: 0,
				time: 875
			}, 'decel');
			scene.showEffect('shine', {
				x: attacker.x + 5,
				y: attacker.y - 5,
				z: attacker.z,
				scale: 0.2,
				opacity: 0.7,
				time: 675
			}, {
				y: attacker.y - 20,
				opacity: 0,
				time: 900
			}, 'decel');
		}
	},
	flight: {
		anim(scene, [attacker, defender]) {
			attacker.anim({
				x: attacker.leftof(-200),
				y: attacker.y + 80,
				z: attacker.z,
				opacity: 0,
				time: 350
			}, 'accel');
			attacker.anim({
				x: defender.leftof(-200),
				y: defender.y + 80,
				z: defender.z,
				time: 1
			}, 'linear');
			attacker.anim({
				x: defender.x,
				y: defender.y,
				z: defender.z,
				opacity: 1,
				time: 350
			}, 'accel');
			scene.showEffect('wisp', {
				x: defender.x,
				y: defender.y,
				z: defender.z,
				scale: 0,
				opacity: 0.5,
				time: 700
			}, {
				scale: 2,
				opacity: 0,
				time: 900
			}, 'linear');
			attacker.anim({
				x: defender.leftof(100),
				y: defender.y - 40,
				z: defender.z,
				opacity: 0,
				time: 175
			});
			attacker.anim({
				x: attacker.x,
				y: attacker.y + 40,
				z: attacker.behind(40),
				time: 1
			});
			attacker.anim({
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				time: 250
			}, 'decel');
			defender.delay(700);
			defender.anim({
				z: defender.behind(20),
				time: 100
			}, 'swing');
			defender.anim({
				time: 300
			}, 'swing');
		}
	},
	shake: {
		anim(scene, [attacker]) {
			attacker.anim({x: attacker.x - 10, time: 200});
			attacker.anim({x: attacker.x + 10, time: 300});
			attacker.anim({x: attacker.x, time: 200});
		}
	},
	dance: {
		anim(scene, [attacker]) {
			attacker.anim({x: attacker.x - 10});
			attacker.anim({x: attacker.x + 10});
			attacker.anim({x: attacker.x});
		}
	},
	consume: {
		anim(scene, [defender]) {
			scene.showEffect('wisp', {
				x: defender.leftof(-25),
				y: defender.y + 40,
				z: defender.behind(-20),
				scale: 0.5,
				opacity: 1
			}, {
				x: defender.leftof(-15),
				y: defender.y + 35,
				z: defender.z,
				scale: 0,
				opacity: 0.2,
				time: 500
			}, 'swing', 'fade');

			defender.delay(400);
			defender.anim({
				y: defender.y + 5,
				yscale: 1.1,
				time: 200
			}, 'swing');
			defender.anim({
				time: 200
			}, 'swing');
			defender.anim({
				y: defender.y + 5,
				yscale: 1.1,
				time: 200
			}, 'swing');
			defender.anim({
				time: 200
			}, 'swing');
		}
	},
	leech: {
		anim(scene, [attacker, defender]) {
			scene.showEffect('energyball', {
				x: defender.x - 30,
				y: defender.y - 40,
				z: defender.z,
				scale: 0.2,
				opacity: 0.7,
				time: 0
			}, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				time: 500,
				opacity: 0.1
			}, 'ballistic2', 'fade');
			scene.showEffect('energyball', {
				x: defender.x + 40,
				y: defender.y - 35,
				z: defender.z,
				scale: 0.2,
				opacity: 0.7,
				time: 50
			}, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				time: 550,
				opacity: 0.1
			}, 'linear', 'fade');
			scene.showEffect('energyball', {
				x: defender.x + 20,
				y: defender.y - 25,
				z: defender.z,
				scale: 0.2,
				opacity: 0.7,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				time: 600,
				opacity: 0.1
			}, 'ballistic2Under', 'fade');
		}
	},
	drain: {
		anim(scene, [attacker, defender]) {
			scene.showEffect('energyball', {
				x: defender.x,
				y: defender.y,
				z: defender.z,
				scale: 0.6,
				opacity: 0.6,
				time: 0
			}, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				time: 500,
				opacity: 0
			}, 'ballistic2');
			scene.showEffect('energyball', {
				x: defender.x,
				y: defender.y,
				z: defender.z,
				scale: 0.6,
				opacity: 0.6,
				time: 50
			}, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				time: 550,
				opacity: 0
			}, 'linear');
			scene.showEffect('energyball', {
				x: defender.x,
				y: defender.y,
				z: defender.z,
				scale: 0.6,
				opacity: 0.6,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				time: 600,
				opacity: 0
			}, 'ballistic2Under');
		}
	},
	hydroshot: {
		anim(scene, [attacker, defender]) {
			scene.showEffect('waterwisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.4,
				opacity: 0.3
			}, {
				x: defender.x + 10,
				y: defender.y + 5,
				z: defender.behind(30),
				scale: 1,
				opacity: 0.6
			}, 'decel', 'explode');
			scene.showEffect('waterwisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.4,
				opacity: 0.3,
				time: 75
			}, {
				x: defender.x - 10,
				y: defender.y - 5,
				z: defender.behind(30),
				scale: 1,
				opacity: 0.6
			}, 'decel', 'explode');
			scene.showEffect('waterwisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.4,
				opacity: 0.3,
				time: 150
			}, {
				x: defender.x,
				y: defender.y + 5,
				z: defender.behind(30),
				scale: 1,
				opacity: 0.6
			}, 'decel', 'explode');
		}
	},
	sound: {
		anim(scene, [attacker]) {
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0,
				opacity: 0.7,
				time: 0
			}, {
				z: attacker.behind(-50),
				scale: 5,
				opacity: 0,
				time: 400
			}, 'linear');
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0,
				opacity: 0.7,
				time: 150
			}, {
				z: attacker.behind(-50),
				scale: 5,
				opacity: 0,
				time: 600
			}, 'linear');
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0,
				opacity: 0.7,
				time: 300
			}, {
				z: attacker.behind(-50),
				scale: 5,
				opacity: 0,
				time: 800
			}, 'linear');
		}
	},
	gravity: {
		anim(scene, [attacker]) {
			attacker.anim({
				y: attacker.y - 20,
				yscale: 0.5,
				time: 300
			}, 'decel');
			attacker.delay(200);
			attacker.anim({
				time: 300
			});
		}
	},
	futuresighthit: {
		anim(scene, [defender]) {
			scene.backgroundEffect('#AA44BB', 250, 0.6);
			scene.backgroundEffect('#AA44FF', 250, 0.6, 400);
			defender.anim({
				scale: 1.2,
				time: 100
			});
			defender.anim({
				scale: 1,
				time: 100
			});
			defender.anim({
				scale: 1.4,
				time: 150
			});
			defender.anim({
				scale: 1,
				time: 150
			});
			scene.wait(700);
		}
	},
	doomdesirehit: {
		anim(scene, [defender]) {
			scene.backgroundEffect('#ffffff', 600, 0.6);
			scene.showEffect('fireball', {
				x: defender.x + 40,
				y: defender.y,
				z: defender.z,
				scale: 0,
				opacity: 0.6
			}, {
				scale: 6,
				opacity: 0
			}, 'linear');
			scene.showEffect('fireball', {
				x: defender.x - 40,
				y: defender.y - 20,
				z: defender.z,
				scale: 0,
				opacity: 0.6,
				time: 150
			}, {
				scale: 6,
				opacity: 0
			}, 'linear');
			scene.showEffect('fireball', {
				x: defender.x + 10,
				y: defender.y + 20,
				z: defender.z,
				scale: 0,
				opacity: 0.6,
				time: 300
			}, {
				scale: 6,
				opacity: 0
			}, 'linear');

			defender.delay(100);
			defender.anim({
				x: defender.x - 30,
				time: 75
			});
			defender.anim({
				x: defender.x + 30,
				time: 100
			});
			defender.anim({
				x: defender.x - 30,
				time: 100
			});
			defender.anim({
				x: defender.x + 30,
				time: 100
			});
			defender.anim({
				x: defender.x,
				time: 100
			});
		}
	},
	itemoff: {
		anim(scene, [defender]) {
			scene.showEffect('pokeball', {
				x: defender.x,
				y: defender.y,
				z: defender.z,
				scale: 1,
				opacity: 1
			}, {
				x: defender.x,
				y: defender.y + 40,
				z: defender.behind(70),
				opacity: 0,
				time: 400
			}, 'ballistic2');
		}
	},
	anger: {
		anim(scene, [attacker]) {
			scene.showEffect('angry', {
				x: attacker.x + 20,
				y: attacker.y + 20,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 0
			}, {
				scale: 1,
				opacity: 1,
				time: 300
			}, 'ballistic2Under', 'fade');
			scene.showEffect('angry', {
				x: attacker.x - 20,
				y: attacker.y + 10,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 100
			}, {
				scale: 1,
				opacity: 1,
				time: 400
			}, 'ballistic2Under', 'fade');
			scene.showEffect('angry', {
				x: attacker.x,
				y: attacker.y + 40,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 200
			}, {
				scale: 1,
				opacity: 1,
				time: 500
			}, 'ballistic2Under', 'fade');
		}
	},
	bidecharge: {
		anim(scene, [attacker]) {
			scene.showEffect('wisp', {
				x: attacker.x + 30,
				y: attacker.y,
				z: attacker.z,
				scale: 1,
				opacity: 1,
				time: 0
			}, {
				y: attacker.y + 60,
				opacity: 0.2,
				time: 400
			}, 'linear', 'fade');
			scene.showEffect('wisp', {
				x: attacker.x - 30,
				y: attacker.y,
				z: attacker.z,
				scale: 1,
				opacity: 1,
				time: 100
			}, {
				y: attacker.y + 60,
				opacity: 0.2,
				time: 500
			}, 'linear', 'fade');
			scene.showEffect('wisp', {
				x: attacker.x + 15,
				y: attacker.y,
				z: attacker.z,
				scale: 1,
				opacity: 1,
				time: 200
			}, {
				y: attacker.y + 60,
				opacity: 0.2,
				time: 600
			}, 'linear', 'fade');
			scene.showEffect('wisp', {
				x: attacker.x - 15,
				y: attacker.y,
				z: attacker.z,
				scale: 1,
				opacity: 1,
				time: 300
			}, {
				y: attacker.y + 60,
				opacity: 0.2,
				time: 700
			}, 'linear', 'fade');

			attacker.anim({
				x: attacker.x - 2.5,
				time: 75
			}, 'swing');
			attacker.anim({
				x: attacker.x + 2.5,
				time: 75
			}, 'swing');
			attacker.anim({
				x: attacker.x - 2.5,
				time: 75
			}, 'swing');
			attacker.anim({
				x: attacker.x + 2.5,
				time: 75
			}, 'swing');
			attacker.anim({
				x: attacker.x - 2.5,
				time: 75
			}, 'swing');
			attacker.anim({
				time: 100
			}, 'accel');
		}
	},
	bideunleash: {
		anim(scene, [attacker]) {
			scene.showEffect('fireball', {
				x: attacker.x + 40,
				y: attacker.y,
				z: attacker.z,
				scale: 0,
				opacity: 0.6
			}, {
				scale: 6,
				opacity: 0
			}, 'linear');
			scene.showEffect('fireball', {
				x: attacker.x - 40,
				y: attacker.y - 20,
				z: attacker.z,
				scale: 0,
				opacity: 0.6,
				time: 150
			}, {
				scale: 6,
				opacity: 0
			}, 'linear');
			scene.showEffect('fireball', {
				x: attacker.x + 10,
				y: attacker.y + 20,
				z: attacker.z,
				scale: 0,
				opacity: 0.6,
				time: 300
			}, {
				scale: 6,
				opacity: 0
			}, 'linear');

			attacker.anim({
				x: attacker.x - 30,
				time: 75
			});
			attacker.anim({
				x: attacker.x + 30,
				time: 100
			});
			attacker.anim({
				x: attacker.x - 30,
				time: 100
			});
			attacker.anim({
				x: attacker.x + 30,
				time: 100
			});
			attacker.anim({
				x: attacker.x - 30,
				time: 100
			});
			attacker.anim({
				x: attacker.x + 30,
				time: 100
			});
			attacker.anim({
				x: attacker.x,
				time: 100
			});
		}
	},
	spectralthiefboost: {
		anim(scene, [attacker, defender]) {
			scene.backgroundEffect('linear-gradient(#000000 30%, #440044', 1400, 0.5);
			scene.showEffect('shadowball', {
				x: defender.x,
				y: defender.y - 30,
				z: defender.z,
				scale: 0.5,
				xscale: 0.5,
				yscale: 1,
				opacity: 0.5
			}, {
				scale: 2,
				xscale: 4,
				opacity: 0.1,
				time: 400
			}, 'decel', 'fade');
			scene.showEffect('poisonwisp', {
				x: defender.x,
				y: defender.y - 25,
				z: defender.z,
				scale: 1
			}, {
				x: defender.x + 50,
				scale: 3,
				xscale: 3.5,
				opacity: 0.3,
				time: 500
			}, 'linear', 'fade');
			scene.showEffect('poisonwisp', {
				x: defender.x,
				y: defender.y - 25,
				z: defender.z,
				scale: 1
			}, {
				x: defender.x - 50,
				scale: 3,
				xscale: 3.5,
				opacity: 0.3,
				time: 500
			}, 'linear', 'fade');
			scene.showEffect('shadowball', {
				x: defender.x + 35,
				y: defender.y,
				z: defender.z,
				opacity: 0.4,
				scale: 0.25,
				time: 50
			}, {
				y: defender.y - 40,
				opacity: 0,
				time: 300
			}, 'accel');
			scene.showEffect('shadowball', {
				x: defender.x - 35,
				y: defender.y,
				z: defender.z,
				opacity: 0.4,
				scale: 0.25,
				time: 100
			}, {
				y: defender.y - 40,
				opacity: 0,
				time: 350
			}, 'accel');
			scene.showEffect('shadowball', {
				x: defender.x + 15,
				y: defender.y,
				z: defender.z,
				opacity: 0.4,
				scale: 0.5,
				time: 150
			}, {
				y: defender.y - 40,
				opacity: 0,
				time: 400
			}, 'accel');
			scene.showEffect('shadowball', {
				x: defender.x + 15,
				y: defender.y,
				z: defender.z,
				opacity: 0.4,
				scale: 0.25,
				time: 200
			}, {
				y: defender.y - 40,
				opacity: 0,
				time: 450
			}, 'accel');

			scene.showEffect('poisonwisp', {
				x: defender.x - 50,
				y: defender.y - 40,
				z: defender.z,
				scale: 2,
				opacity: 0.3,
				time: 300
			}, {
				x: attacker.x - 50,
				y: attacker.y - 40,
				z: attacker.z,
				time: 900
			}, 'decel', 'fade');
			scene.showEffect('poisonwisp', {
				x: defender.x - 50,
				y: defender.y - 40,
				z: defender.z,
				scale: 2,
				opacity: 0.3,
				time: 400
			}, {
				x: attacker.x - 50,
				y: attacker.y - 40,
				z: attacker.z,
				time: 900
			}, 'decel', 'fade');
			scene.showEffect('poisonwisp', {
				x: defender.x,
				y: defender.y - 40,
				z: defender.z,
				scale: 2,
				opacity: 0.3,
				time: 450
			}, {
				x: attacker.x,
				y: attacker.y - 40,
				z: attacker.z,
				time: 950
			}, 'decel', 'fade');

			scene.showEffect('shadowball', {
				x: attacker.x,
				y: attacker.y - 30,
				z: attacker.z,
				scale: 0,
				xscale: 0.5,
				yscale: 1,
				opacity: 0.5,
				time: 750
			}, {
				scale: 2,
				xscale: 4,
				opacity: 0.1,
				time: 1200
			}, 'decel', 'fade');

			scene.showEffect('shadowball', {
				x: attacker.x + 35,
				y: attacker.y - 40,
				z: attacker.z,
				opacity: 0.4,
				scale: 0.25,
				time: 750
			}, {
				y: attacker.y,
				opacity: 0,
				time: 1000
			}, 'decel');
			scene.showEffect('shadowball', {
				x: attacker.x - 35,
				y: attacker.y - 40,
				z: attacker.z,
				opacity: 1,
				scale: 0.25,
				time: 800
			}, {
				y: attacker.y,
				opacity: 0,
				time: 1150
			}, 'decel');
			scene.showEffect('shadowball', {
				x: attacker.x + 15,
				y: attacker.y - 40,
				z: attacker.z,
				opacity: 1,
				scale: 0.25,
				time: 950
			}, {
				y: attacker.y,
				opacity: 0,
				time: 1200
			}, 'decel');
			scene.showEffect('shadowball', {
				x: attacker.x + 15,
				y: attacker.y - 40,
				z: attacker.z,
				opacity: 1,
				scale: 0.25,
				time: 1000
			}, {
				y: attacker.y,
				opacity: 0,
				time: 1350
			}, 'decel');

			scene.showEffect('poisonwisp', {
				x: attacker.x,
				y: attacker.y - 25,
				z: attacker.z,
				scale: 2,
				opacity: 1,
				time: 750
			}, {
				x: attacker.x + 75,
				opacity: 0.3,
				time: 1200
			}, 'linear', 'fade');
			scene.showEffect('poisonwisp', {
				x: attacker.x,
				y: attacker.y - 25,
				z: attacker.z,
				scale: 2,
				opacity: 1,
				time: 750
			}, {
				x: attacker.x - 75,
				opacity: 0.3,
				time: 1200
			}, 'linear', 'fade');

			defender.anim({
				x: defender.x - 15,
				time: 75
			});
			defender.anim({
				x: defender.x + 15,
				time: 100
			});
			defender.anim({
				x: defender.x - 15,
				time: 100
			});
			defender.anim({
				x: defender.x + 15,
				time: 100
			});
			defender.anim({
				x: defender.x - 15,
				time: 100
			});
			defender.anim({
				x: defender.x + 15,
				time: 100
			});
			defender.anim({
				x: defender.x,
				time: 100
			});
		}
	},
	schoolingin: {
		anim(scene, [attacker]) {
			scene.backgroundEffect('#0000DD', 600, 0.2);
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 2.5,
				opacity: 1
			}, {
				scale: 3,
				time: 600
			}, 'linear', 'explode');
			scene.showEffect('waterwisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 3,
				opacity: 0.3
			}, {
				scale: 3.25,
				time: 600
			}, 'linear', 'explode');

			scene.showEffect('iceball', {
				x: attacker.leftof(200),
				y: attacker.y + 40,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5
			}, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				opacity: 0,
				time: 200
			}, 'ballistic', 'fade');
			scene.showEffect('iceball', {
				x: attacker.leftof(-140),
				y: attacker.y - 60,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				opacity: 0,
				time: 300
			}, 'ballistic2Under', 'fade');
			scene.showEffect('iceball', {
				x: attacker.leftof(-140),
				y: attacker.y + 50,
				z: attacker.behind(170),
				scale: 0.5,
				opacity: 0.5,
				time: 200
			}, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				opacity: 0,
				time: 400
			}, 'ballistic2', 'fade');
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y + 30,
				z: attacker.behind(-250),
				scale: 0.5,
				opacity: 0.5,
				time: 200
			}, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				opacity: 0,
				time: 500
			}, 'ballistic', 'fade');
			scene.showEffect('iceball', {
				x: attacker.leftof(240),
				y: attacker.y - 80,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 300
			}, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				opacity: 0,
				time: 600
			}, 'ballistic2Under');
		}
	},
	schoolingout: {
		anim(scene, [attacker]) {
			scene.backgroundEffect('#0000DD', 600, 0.2);
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 3,
				opacity: 1
			}, {
				scale: 2,
				time: 600
			}, 'linear', 'explode');
			scene.showEffect('waterwisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 3.25,
				opacity: 0.3
			}, {
				scale: 2.5,
				time: 600
			}, 'linear', 'explode');

			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 0
			}, {
				x: attacker.leftof(200),
				y: attacker.y + 40,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 200
			}, 'ballistic', 'fade');
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 0,
				time: 100
			}, {
				x: attacker.leftof(-140),
				y: attacker.y - 60,
				z: attacker.z,
				opacity: 0.5,
				time: 300
			}, 'ballistic2Under', 'fade');
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 0,
				time: 200
			}, {
				x: attacker.leftof(-140),
				y: attacker.y + 50,
				z: attacker.behind(170),
				opacity: 0.5,
				time: 400
			}, 'ballistic2', 'fade');
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 0,
				time: 200
			}, {
				x: attacker.x,
				y: attacker.y + 30,
				z: attacker.behind(-250),
				opacity: 0.5,
				time: 500
			}, 'ballistic', 'fade');
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 0,
				time: 300
			}, {
				x: attacker.leftof(240),
				y: attacker.y - 80,
				z: attacker.z,
				opacity: 0.5,
				time: 600
			}, 'ballistic2Under');
		}
	},
	primalalpha: {
		anim(scene, [attacker]) {
			scene.backgroundEffect('#0000DD', 500, 0.4);
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 2,
				opacity: 0.2,
				time: 0
			}, {
				scale: 0.5,
				opacity: 1,
				time: 300
			}, 'linear', 'fade');
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 1,
				time: 300
			}, {
				scale: 4,
				opacity: 0,
				time: 700
			}, 'linear', 'fade');
			scene.showEffect('shadowball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 300
			}, {
				scale: 5,
				opacity: 0,
				time: 600
			}, 'linear', 'fade');
			scene.showEffect('alpha', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 1,
				time: 300
			}, {
				scale: 2.5,
				opacity: 0,
				time: 600
			}, 'decel');
		}
	},
	primalomega: {
		anim(scene, [attacker]) {
			scene.backgroundEffect('linear-gradient(#390000 30%, #B84038)', 500, 0.4);
			scene.showEffect('flareball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 2,
				opacity: 0.2,
				time: 0
			}, {
				scale: 0.5,
				opacity: 1,
				time: 300
			}, 'linear', 'fade');
			scene.showEffect('flareball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 1,
				time: 300
			}, {
				scale: 4,
				opacity: 0,
				time: 700
			}, 'linear', 'fade');
			scene.showEffect('shadowball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 300
			}, {
				scale: 5,
				opacity: 0,
				time: 600
			}, 'linear', 'fade');
			scene.showEffect('omega', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 1,
				time: 300
			}, {
				scale: 2.5,
				opacity: 0,
				time: 600
			}, 'decel');
		}
	},
	megaevo: {
		anim(scene, [attacker]) {
			scene.backgroundEffect('#835BA5', 500, 0.6);
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 2,
				opacity: 0.2,
				time: 0
			}, {
				scale: 0.5,
				opacity: 1,
				time: 300
			}, 'linear', 'fade');
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 1,
				time: 300
			}, {
				scale: 4,
				opacity: 0,
				time: 700
			}, 'linear', 'fade');
			scene.showEffect('mistball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 300
			}, {
				scale: 5,
				opacity: 0,
				time: 600
			}, 'linear', 'fade');
			scene.showEffect('rainbow', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 1,
				time: 300
			}, {
				scale: 5,
				opacity: 0,
				time: 600
			}, 'linear', 'fade');
		}
	},
	zpower: {
		anim(scene, [attacker]) {
			scene.backgroundEffect('linear-gradient(#000000 20%, #0000DD)', 1800, 0.4);
			scene.showEffect('electroball', {
				x: attacker.x - 60,
				y: attacker.y + 40,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 0
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 300
			}, 'linear', 'fade');
			scene.showEffect('electroball', {
				x: attacker.x + 60,
				y: attacker.y - 5,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 300
			}, 'linear', 'fade');
			scene.showEffect('electroball', {
				x: attacker.x - 30,
				y: attacker.y + 60,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 400
			}, 'linear', 'fade');
			scene.showEffect('electroball', {
				x: attacker.x + 20,
				y: attacker.y - 50,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 400
			}, 'linear', 'fade');
			scene.showEffect('electroball', {
				x: attacker.x - 70,
				y: attacker.y - 50,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 200
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 500
			}, 'linear', 'fade');
			scene.showEffect('zsymbol', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.7,
				opacity: 1,
				time: 500
			}, {
				scale: 1,
				opacity: 0.5,
				time: 800
			}, 'decel', 'explode');
			scene.showEffect(attacker.sp, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				opacity: 0.3,
				time: 800
			}, {
				y: attacker.y + 20,
				scale: 2,
				opacity: 0,
				time: 1200
			}, 'accel');
			scene.showEffect(attacker.sp, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				opacity: 0.3,
				time: 1000
			}, {
				y: attacker.y + 20,
				scale: 2,
				opacity: 0,
				time: 1400
			}, 'accel');
			scene.showEffect(attacker.sp, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				opacity: 0.3,
				time: 1200
			}, {
				y: attacker.y + 20,
				scale: 2,
				opacity: 0,
				time: 1600
			}, 'accel');
		}
	},
	powerconstruct: {
		anim(scene, [attacker]) {
			var xf = [1, -1, 1, -1];
			var yf = [1, -1, -1, 1];
			var xf2 = [1, 0, -1, 0];
			var yf2 = [0, 1, 0, -1];

			scene.backgroundEffect('#000000', 1000, 0.7);
			for (var i = 0; i < 4; i++) {
				scene.showEffect('energyball', {
					x: attacker.x + 150 * xf[i],
					y: attacker.y - 50,
					z: attacker.z + 70 * yf[i],
					scale: 0.1,
					xscale: 0.5,
					opacity: 0.4
				}, {
					x: attacker.x,
					y: attacker.y - 50,
					z: attacker.z,
					scale: 0.3,
					xscale: 0.8,
					opacity: 0,
					time: 500
				}, 'decel', 'fade');
				scene.showEffect('energyball', {
					x: attacker.x + 200 * xf2[i],
					y: attacker.y - 50,
					z: attacker.z + 90 * yf2[i],
					scale: 0.1,
					xscale: 0.5,
					opacity: 0.4
				}, {
					x: attacker.x,
					y: attacker.y - 50,
					z: attacker.z,
					scale: 0.3,
					xscale: 0.8,
					opacity: 0,
					time: 500
				}, 'decel', 'fade');

				scene.showEffect('energyball', {
					x: attacker.x + 50 * xf[i],
					y: attacker.y - 50,
					z: attacker.z + 100 * yf[i],
					scale: 0.1,
					xscale: 0.5,
					opacity: 0.4,
					time: 200
				}, {
					x: attacker.x,
					y: attacker.y - 50,
					z: attacker.z,
					scale: 0.3,
					xscale: 0.8,
					opacity: 0,
					time: 500
				}, 'decel', 'fade');
				scene.showEffect('energyball', {
					x: attacker.x + 100 * xf2[i],
					y: attacker.y - 50,
					z: attacker.z + 90 * yf2[i],
					scale: 0.1,
					xscale: 0.5,
					opacity: 0.4,
					time: 200
				}, {
					x: attacker.x,
					y: attacker.y - 50,
					z: attacker.z,
					scale: 0.3,
					xscale: 0.8,
					opacity: 0,
					time: 500
				}, 'decel', 'fade');
			}
			scene.showEffect('energyball', {
				x: attacker.x,
				y: attacker.y - 25,
				z: attacker.z,
				scale: 3,
				opacity: 0,
				time: 50
			}, {
				scale: 1,
				opacity: 0.8,
				time: 300
			}, 'linear', 'fade');
			scene.showEffect('energyball', {
				x: attacker.x,
				y: attacker.y - 25,
				z: attacker.z,
				scale: 3.5,
				opacity: 0,
				time: 150
			}, {
				scale: 1.5,
				opacity: 1,
				time: 350
			}, 'linear', 'fade');
			scene.showEffect('energyball', {
				x: attacker.x,
				y: attacker.y - 25,
				z: attacker.z,
				scale: 0.5,
				opacity: 1,
				time: 200
			}, {
				scale: 3,
				opacity: 0,
				time: 600
			}, 'linear', 'fade');
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y - 25,
				z: attacker.z,
				scale: 1,
				opacity: 0.6,
				time: 100
			}, {
				scale: 3.5,
				opacity: 0.8,
				time: 500
			}, 'linear', 'explode');
		}
	},
	ultraburst: {
		anim(scene, [attacker]) {
			scene.backgroundEffect('#000000', 600, 0.5);
			scene.backgroundEffect('#ffffff', 500, 1, 550);
			scene.showEffect('wisp', {
				x: attacker.x - 60,
				y: attacker.y + 40,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 0
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 150
			}, 'linear', 'fade');
			scene.showEffect('wisp', {
				x: attacker.x + 60,
				y: attacker.y - 5,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 150
			}, 'linear', 'fade');
			scene.showEffect('wisp', {
				x: attacker.x - 30,
				y: attacker.y + 60,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 250
			}, 'linear', 'fade');
			scene.showEffect('wisp', {
				x: attacker.x + 20,
				y: attacker.y - 50,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 250
			}, 'linear', 'fade');
			scene.showEffect('wisp', {
				x: attacker.x - 70,
				y: attacker.y - 50,
				z: attacker.z,
				scale: 0.7,
				opacity: 0.7,
				time: 100
			}, {
				x: attacker.x,
				y: attacker.y,
				scale: 0.2,
				opacity: 0.2,
				time: 300
			}, 'linear', 'fade');

			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 1.5,
				opacity: 1
			}, {
				scale: 4,
				time: 600
			}, 'linear', 'explode');
			scene.showEffect('electroball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 2,
				opacity: 0
			}, {
				scale: 2.25,
				opacity: 0.1,
				time: 600
			}, 'linear', 'explode');
			scene.showEffect('energyball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 2,
				opacity: 0,
				time: 200
			}, {
				scale: 2.25,
				opacity: 0.1,
				time: 600
			}, 'linear', 'explode');

			scene.showEffect('electroball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 6,
				opacity: 0.2
			}, {
				scale: 1,
				opacity: 0,
				time: 300
			}, 'linear');
			scene.showEffect('electroball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 6,
				opacity: 0.2,
				time: 150
			}, {
				scale: 1,
				opacity: 0,
				time: 450
			}, 'linear');
			scene.showEffect('electroball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 6,
				opacity: 0.2,
				time: 300
			}, {
				scale: 1,
				opacity: 0,
				time: 600
			}, 'linear');
			scene.showEffect('ultra', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 1,
				time: 600
			}, {
				scale: 1,
				opacity: 0,
				time: 900
			}, 'decel');
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y - 60,
				z: attacker.z,
				scale: 0.5,
				xscale: 0.25,
				yscale: 0,
				opacity: 0.5,
				time: 600
			}, {
				scale: 2,
				xscale: 6,
				yscale: 1,
				opacity: 0,
				time: 800
			}, 'linear');
			scene.showEffect('iceball', {
				x: attacker.x,
				y: attacker.y - 60,
				z: attacker.z,
				scale: 0.5,
				xscale: 0.25,
				yscale: 0.75,
				opacity: 0.5,
				time: 800
			}, {
				scale: 2,
				xscale: 6,
				opacity: 0.1,
				time: 1000
			}, 'linear');
		}
	}
};
var BattleStatusAnims: AnimTable = {
	brn: {
		anim(scene, [attacker]) {
			scene.showEffect('fireball', {
				x: attacker.x - 20,
				y: attacker.y - 15,
				z: attacker.z,
				scale: 0.2,
				opacity: 0.3
			}, {
				x: attacker.x + 40,
				y: attacker.y + 15,
				z: attacker.z,
				scale: 1,
				opacity: 1,
				time: 300
			}, 'swing', 'fade');
		}
	},
	psn: {
		anim(scene, [attacker]) {
			scene.showEffect('poisonwisp', {
				x: attacker.x + 30,
				y: attacker.y - 40,
				z: attacker.z,
				scale: 0.2,
				opacity: 1,
				time: 0
			}, {
				y: attacker.y,
				scale: 1,
				opacity: 0.5,
				time: 300
			}, 'decel', 'fade');
			scene.showEffect('poisonwisp', {
				x: attacker.x - 30,
				y: attacker.y - 40,
				z: attacker.z,
				scale: 0.2,
				opacity: 1,
				time: 100
			}, {
				y: attacker.y,
				scale: 1,
				opacity: 0.5,
				time: 400
			}, 'decel', 'fade');
			scene.showEffect('poisonwisp', {
				x: attacker.x,
				y: attacker.y - 40,
				z: attacker.z,
				scale: 0.2,
				opacity: 1,
				time: 200
			}, {
				y: attacker.y,
				scale: 1,
				opacity: 0.5,
				time: 500
			}, 'decel', 'fade');
		}
	},
	slp: {
		anim(scene, [attacker]) {
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y + 20,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.1
			}, {
				x: attacker.x,
				y: attacker.y + 20,
				z: attacker.behind(-50),
				scale: 1.5,
				opacity: 1,
				time: 400
			}, 'ballistic2Under', 'fade');
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y + 20,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.1,
				time: 200
			}, {
				x: attacker.x,
				y: attacker.y + 20,
				z: attacker.behind(-50),
				scale: 1.5,
				opacity: 1,
				time: 600
			}, 'ballistic2Under', 'fade');
		}
	},
	par: {
		anim(scene, [attacker]) {
			scene.showEffect('electroball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 1.5,
				opacity: 0.2
			}, {
				scale: 2,
				opacity: 0.1,
				time: 300
			}, 'linear', 'fade');

			attacker.delay(100);
			attacker.anim({
				x: attacker.x - 1,
				time: 75
			}, 'swing');
			attacker.anim({
				x: attacker.x + 1,
				time: 75
			}, 'swing');
			attacker.anim({
				x: attacker.x - 1,
				time: 75
			}, 'swing');
			attacker.anim({
				x: attacker.x + 1,
				time: 75
			}, 'swing');
			attacker.anim({
				x: attacker.x - 1,
				time: 75
			}, 'swing');
			attacker.anim({
				time: 100
			}, 'accel');
		}
	},
	frz: {
		anim(scene, [attacker]) {
			scene.showEffect('icicle', {
				x: attacker.x - 30,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 200
			}, {
				scale: 0.9,
				opacity: 0,
				time: 600
			}, 'linear', 'fade');
			scene.showEffect('icicle', {
				x: attacker.x,
				y: attacker.y - 30,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 300
			}, {
				scale: 0.9,
				opacity: 0,
				time: 650
			}, 'linear', 'fade');
			scene.showEffect('icicle', {
				x: attacker.x + 15,
				y: attacker.y,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 400
			}, {
				scale: 0.9,
				opacity: 0,
				time: 700
			}, 'linear', 'fade');
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 1,
				opacity: 0.5
			}, {
				scale: 3,
				opacity: 0,
				time: 600
			}, 'linear', 'fade');
		}
	},
	flinch: {
		anim(scene, [attacker]) {
			scene.showEffect('shadowball', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 1,
				opacity: 0.2
			}, {
				scale: 3,
				opacity: 0.1,
				time: 300
			}, 'linear', 'fade');
		}
	},
	attracted: {
		anim(scene, [attacker]) {
			scene.showEffect('heart', {
				x: attacker.x + 20,
				y: attacker.y + 20,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 0
			}, {
				scale: 1,
				opacity: 1,
				time: 300
			}, 'ballistic2Under', 'fade');
			scene.showEffect('heart', {
				x: attacker.x - 20,
				y: attacker.y + 10,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 100
			}, {
				scale: 1,
				opacity: 1,
				time: 400
			}, 'ballistic2Under', 'fade');
			scene.showEffect('heart', {
				x: attacker.x,
				y: attacker.y + 40,
				z: attacker.z,
				scale: 0.5,
				opacity: 0.5,
				time: 200
			}, {
				scale: 1,
				opacity: 1,
				time: 500
			}, 'ballistic2Under', 'fade');
		}
	},
	cursed: {
		anim(scene, [attacker]) {
			scene.backgroundEffect('#000000', 700, 0.2);
			attacker.delay(300);
			attacker.anim({x: attacker.x - 5, time: 50});
			attacker.anim({x: attacker.x + 5, time: 50});
			attacker.anim({x: attacker.x - 5, time: 50});
			attacker.anim({x: attacker.x + 5, time: 50});
			attacker.anim({x: attacker.x, time: 50});

			scene.showEffect(attacker.sp, {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				opacity: 0.5,
				time: 0
			}, {
				z: attacker.behind(20),
				opacity: 0,
				time: 600
			}, 'decel');
		}
	},
	confused: {
		anim(scene, [attacker]) {
			scene.showEffect('electroball', {
				x: attacker.x + 50,
				y: attacker.y + 30,
				z: attacker.z,
				scale: 0.1,
				opacity: 1,
				time: 400
			}, {
				x: attacker.x - 50,
				scale: 0.15,
				opacity: 0.4,
				time: 600
			}, 'linear', 'fade');
			scene.showEffect('electroball', {
				x: attacker.x - 50,
				y: attacker.y + 30,
				z: attacker.z,
				scale: 0.1,
				opacity: 1,
				time: 400
			}, {
				x: attacker.x + 50,
				scale: 0.15,
				opacity: 0.4,
				time: 600
			}, 'linear', 'fade');
			scene.showEffect('electroball', {
				x: attacker.x + 50,
				y: attacker.y + 30,
				z: attacker.z,
				scale: 0.1,
				opacity: 1,
				time: 600
			}, {
				x: attacker.x - 50,
				scale: 0.4,
				opacity: 0.4,
				time: 800
			}, 'linear', 'fade');
			scene.showEffect('electroball', {
				x: attacker.x - 50,
				y: attacker.y + 30,
				z: attacker.z,
				scale: 0.15,
				opacity: 1,
				time: 600
			}, {
				x: attacker.x + 50,
				scale: 0.4,
				opacity: 0.4,
				time: 800
			}, 'linear', 'fade');
		}
	},
	confusedselfhit: {
		anim(scene, [attacker]) {
			scene.showEffect('wisp', {
				x: attacker.x,
				y: attacker.y,
				z: attacker.z,
				scale: 0,
				opacity: 0.5
			}, {
				scale: 2,
				opacity: 0,
				time: 200
			}, 'linear');
			attacker.delay(50);
			attacker.anim({
				x: attacker.leftof(2),
				z: attacker.behind(5),
				time: 100
			}, 'swing');
			attacker.anim({
				time: 300
			}, 'swing');
		}
	}
};
BattleStatusAnims['focuspunch'] = {anim: BattleStatusAnims['flinch'].anim};

