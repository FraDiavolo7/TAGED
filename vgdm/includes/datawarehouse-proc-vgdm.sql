CREATE OR REPLACE FUNCTION generate_dplayer() returns void AS $$
DECLARE
  tplayer_id1 INTEGER := NULL;
  tplayer_id2 INTEGER := NULL;
  tplayer_id3 INTEGER := NULL;
  tplayer_id4 INTEGER := NULL;
  tplayer_id5 INTEGER := NULL;
  tplayer_id6 INTEGER := NULL;
  tplayer_id7 INTEGER := NULL;
  tplayer_id8 INTEGER := NULL;
  tplayer_loc VARCHAR(127) := NULL;
  Temp INTEGER := 1;
  Sing BOOLEAN := FALSE;
  Id INTEGER := 1;
  curs RECORD;
  curs_tmp RECORD;
BEGIN
  TRUNCATE TABLE DPlayerHierarchy;

  FOR curs IN SELECT * FROM VDPlayer
  LOOP
    -- dbms_output.put_line('player_loc_id = '||curs.player_loc_id);
    FOR i IN 1..8
    LOOP
      --SELECT last_value + 1 INTO Id FROM dplayer_player_loc_id_seq;

      -- dbms_output.put_line('curs2.player_loc_id = '||curs2.player_loc_id);
      IF (TEMP = 1) THEN
        tplayer_id1 := Id;
        tplayer_loc := curs.country;

        FOR curs_tmp IN SELECT COUNT(*) AS nb FROM VDPlayer
        WHERE country = curs.country
        LOOP
          IF (curs_tmp.nb = 1) THEN
            Sing := TRUE;
          END IF;
        END LOOP;
      END IF;
      IF (TEMP = 2) THEN
        tplayer_id2 := Id;
        tplayer_loc := curs.region;

        FOR curs_tmp IN SELECT COUNT(*) AS nb FROM VDPlayer
                        WHERE country = curs.country
                              AND region = curs.region
        LOOP
          IF (curs_tmp.nb = 1) THEN
            Sing := TRUE;
          END IF;
        END LOOP;
      END IF;
      IF (TEMP = 3) THEN
        tplayer_id3 := Id;
        tplayer_loc := curs.city;

        FOR curs_tmp IN SELECT COUNT(*) AS nb FROM VDPlayer
                        WHERE country = curs.country
                              AND region = curs.region
                              AND city = curs.city
        LOOP
          IF (curs_tmp.nb = 1) THEN
            Sing := TRUE;
          END IF;
        END LOOP;
      END IF;
      IF (TEMP = 4) THEN
        tplayer_id4 := Id;
        tplayer_loc := curs.ipaddress;

        FOR curs_tmp IN SELECT COUNT(*) AS nb FROM VDPlayer
                        WHERE country = curs.country
                              AND region = curs.region
                              AND city = curs.city
                              AND ipaddress = curs.ipaddress
        LOOP
          IF (curs_tmp.nb = 1) THEN
            Sing := TRUE;
          END IF;
        END LOOP;
      END IF;
      IF (TEMP = 5) THEN
        tplayer_id5 := Id;
        tplayer_loc := curs.platform;

        FOR curs_tmp IN SELECT COUNT(*) AS nb FROM VDPlayer
                        WHERE country = curs.country
                              AND region = curs.region
                              AND city = curs.city
                              AND ipaddress = curs.ipaddress
                              AND platform = curs.platform
        LOOP
          IF (curs_tmp.nb = 1) THEN
            Sing := TRUE;
          END IF;
        END LOOP;
      END IF;
      IF (TEMP = 6) THEN
        tplayer_id6 := Id;
        tplayer_loc := curs.browser;

        FOR curs_tmp IN SELECT COUNT(*) AS nb FROM VDPlayer
                        WHERE country = curs.country
                              AND region = curs.region
                              AND city = curs.city
                              AND ipaddress = curs.ipaddress
                              AND platform = curs.platform
                              AND browser = curs.browser
        LOOP
          IF (curs_tmp.nb = 1) THEN
            Sing := TRUE;
          END IF;
        END LOOP;
      END IF;
      IF (TEMP = 7) THEN
        tplayer_id7 := Id;
        tplayer_loc := curs.lang;

        FOR curs_tmp IN SELECT COUNT(*) AS nb FROM VDPlayer
                        WHERE country = curs.country
                         AND region = curs.region
                         AND city = curs.city
                         AND ipaddress = curs.ipaddress
                         AND platform = curs.platform
                         AND browser = curs.browser
                         AND lang = curs.lang
        LOOP
          IF (curs_tmp.nb = 1) THEN
            Sing := TRUE;
          END IF;
        END LOOP;
      END IF;
      IF (TEMP = 8) THEN
        tplayer_id8 := Id;
        tplayer_loc := curs.player_loc;

        FOR curs_tmp IN SELECT COUNT(*) AS nb FROM VDPlayer
                        WHERE country = curs.country
                          AND region = curs.region
                          AND city = curs.city
                          AND ipaddress = curs.ipaddress
                          AND platform = curs.platform
                          AND browser = curs.browser
                          AND lang = curs.lang
                          AND player_loc = curs.player_loc
        LOOP
          IF (curs_tmp.nb = 1) THEN
            Sing := TRUE;
          END IF;
        END LOOP;
      END IF;

      IF NOT EXISTS (SELECT 1 FROM DPlayer WHERE player_loc = tplayer_loc) OR (Sing = TRUE)
      THEN
        BEGIN
          INSERT INTO DPlayer VALUES (DEFAULT, tplayer_id1, tplayer_id2, tplayer_id3, tplayer_id4, tplayer_id5, tplayer_id6, tplayer_id7, tplayer_id8, tplayer_loc);
          EXCEPTION
          WHEN unique_violation THEN
          -- ne rien faire.
        END;

        IF (TEMP = 1) THEN
          INSERT INTO DPlayerHierarchy VALUES (Id, 0, tplayer_loc);
        END IF;
        IF (TEMP = 2) THEN
          INSERT INTO DPlayerHierarchy VALUES (Id, tplayer_id1, tplayer_loc);
        END IF;
        IF (TEMP = 3) THEN
          INSERT INTO DPlayerHierarchy VALUES (Id, tplayer_id2, tplayer_loc);
        END IF;
        IF (TEMP = 4) THEN
          INSERT INTO DPlayerHierarchy VALUES (Id, tplayer_id3, tplayer_loc);
        END IF;
        IF (TEMP = 5) THEN
          INSERT INTO DPlayerHierarchy VALUES (Id, tplayer_id4, tplayer_loc);
        END IF;
        IF (TEMP = 6) THEN
          INSERT INTO DPlayerHierarchy VALUES (Id, tplayer_id5, tplayer_loc);
        END IF;
        IF (TEMP = 7) THEN
          INSERT INTO DPlayerHierarchy VALUES (Id, tplayer_id6, tplayer_loc);
        END IF;
        IF (TEMP = 8) THEN
          INSERT INTO DPlayerHierarchy VALUES (Id, tplayer_id7, tplayer_loc);
        END IF;

        Id := Id + 1;
      ELSE
        IF (TEMP = 1) THEN
          SELECT player_loc_id INTO tplayer_id1 FROM DPlayer WHERE player_loc = tplayer_loc;
        END IF;
        IF (TEMP = 2) THEN
          SELECT player_loc_id INTO tplayer_id2 FROM DPlayer WHERE player_loc = tplayer_loc;
        END IF;
        IF (TEMP = 3) THEN
          SELECT player_loc_id INTO tplayer_id3 FROM DPlayer WHERE player_loc = tplayer_loc;
        END IF;
        IF (TEMP = 4) THEN
          SELECT player_loc_id INTO tplayer_id4 FROM DPlayer WHERE player_loc = tplayer_loc;
        END IF;
        IF (TEMP = 5) THEN
          SELECT player_loc_id INTO tplayer_id5 FROM DPlayer WHERE player_loc = tplayer_loc;
        END IF;
        IF (TEMP = 6) THEN
          SELECT player_loc_id INTO tplayer_id6 FROM DPlayer WHERE player_loc = tplayer_loc;
        END IF;
        IF (TEMP = 7) THEN
          SELECT player_loc_id INTO tplayer_id7 FROM DPlayer WHERE player_loc = tplayer_loc;
        END IF;
        IF (TEMP = 8) THEN
          SELECT player_loc_id INTO tplayer_id8 FROM DPlayer WHERE player_loc = tplayer_loc;
        END IF;
      END IF;

      Temp := Temp + 1;
      Sing := FALSE;
    END LOOP;
    tplayer_id1 := NULL;
    tplayer_id2 := NULL;
    tplayer_id3 := NULL;
    tplayer_id4 := NULL;
    tplayer_id5 := NULL;
    tplayer_id6 := NULL;
    tplayer_id7 := NULL;
    tplayer_id8 := NULL;
    tplayer_loc := NULL;
    Temp := 1;
  END LOOP;
END;
$$ LANGUAGE plpgsql;

-- **********************************************

CREATE OR REPLACE FUNCTION generate_dround() returns void AS $$
DECLARE
  tround_id1 INTEGER := NULL;
  tround_id2 INTEGER := NULL;
  tround_turn INTEGER := NULL;
  tround_date TIMESTAMP WITHOUT TIME ZONE := NULL;
  Temp INTEGER := 1;
  Sing BOOLEAN := FALSE;
  Id INTEGER := 1;
  curs RECORD;
  curs_tmp RECORD;
BEGIN
  TRUNCATE TABLE DRoundHierarchy;

  FOR curs IN SELECT * FROM VDRound
  LOOP
    FOR i IN 1..2
    LOOP
      IF (TEMP = 1) THEN
        tround_id1 := Id;
        tround_turn := curs.game_id;
        tround_date := curs.game_date;

        FOR curs_tmp IN SELECT COUNT(*) AS nb FROM VDRound
                        WHERE game_id = curs.game_id
        LOOP
          IF (curs_tmp.nb = 1) THEN
            Sing := TRUE;
          END IF;
        END LOOP;
      END IF;
      IF (TEMP = 2) THEN
        tround_id2 := Id;
        tround_turn := curs.round_num;
        tround_date := curs.round_date;

        FOR curs_tmp IN SELECT COUNT(*) AS nb FROM VDRound
                        WHERE game_id = curs.game_id
                          AND round_num = curs.round_num
        LOOP
          IF (curs_tmp.nb = 1) THEN
            Sing := TRUE;
          END IF;
        END LOOP;
      END IF;

      IF NOT EXISTS (SELECT 1 FROM DRound WHERE turn = tround_turn) OR (Sing = TRUE)
      THEN
        BEGIN
          INSERT INTO DRound VALUES (DEFAULT, tround_id1, tround_id2, tround_turn, tround_date);
          EXCEPTION
          WHEN unique_violation THEN
          -- ne rien faire.
        END;

        IF (TEMP = 1) THEN
          INSERT INTO DRoundHierarchy VALUES (Id, 0, tround_turn);
        END IF;
        IF (TEMP = 2) THEN
          INSERT INTO DRoundHierarchy VALUES (Id, tround_id1, tround_turn);
        END IF;

        Id := Id + 1;
      ELSE
        IF (TEMP = 1) THEN
          SELECT round_date_id INTO tround_id1 FROM DRound WHERE turn = tround_turn;
        END IF;
        IF (TEMP = 2) THEN
          SELECT round_date_id INTO tround_id2 FROM DRound WHERE turn = tround_turn;
        END IF;
      END IF;

      TEMP := TEMP + 1;
      Sing := FALSE;
    END LOOP;
    tround_id1 := NULL;
    tround_id2 := NULL;
    tround_turn := NULL;
    tround_date := NULL;
    Temp := 1;
  END LOOP;
END;
$$ LANGUAGE plpgsql;

-- **********************************************

CREATE OR REPLACE FUNCTION generate_dmatch() returns void AS $$
DECLARE
  tmatch_id1 INTEGER := NULL;
  tmatch_id2 INTEGER := NULL;
  tmatch_elem INTEGER := NULL;
  tmatch_color VARCHAR(7) := NULL;
  tmatch_four BOOLEAN := FALSE;
  tmatch_beam BOOLEAN := FALSE;
  tmatch_date TIMESTAMP WITHOUT TIME ZONE := NULL;
  Temp INTEGER := 1;
  Sing BOOLEAN := FALSE;
  Id INTEGER := 1;
  curs RECORD;
  curs_tmp RECORD;
BEGIN
  TRUNCATE TABLE DMatchHierarchy;

  FOR curs IN SELECT * FROM VDMatch
  LOOP
    FOR i IN 1..2
    LOOP
      IF (TEMP = 1) THEN
        tmatch_id1 := Id;
        tmatch_elem := curs.stroke_id;
        tmatch_date := curs.stroke_date;

        FOR curs_tmp IN SELECT COUNT(*) AS nb FROM VDMatch
                        WHERE stroke_id = curs.stroke_id
        LOOP
          IF (curs_tmp.nb = 1) THEN
            Sing := TRUE;
          END IF;
        END LOOP;

        tmatch_color := NULL;
        tmatch_four := FALSE;
        tmatch_beam := FALSE;
      END IF;
      IF (TEMP = 2) THEN
        tmatch_id2 := Id;
        tmatch_elem := curs.match_num;
        tmatch_date := curs.match_date;

        FOR curs_tmp IN SELECT COUNT(*) AS nb FROM VDMatch
                        WHERE stroke_id = curs.stroke_id
                          AND match_num = curs.match_num
        LOOP
          IF (curs_tmp.nb = 1) THEN
            Sing := TRUE;
          END IF;
        END LOOP;

        tmatch_color := curs.color;
        tmatch_four := curs.special_four;
        tmatch_beam := curs.beam;
      END IF;

      IF NOT EXISTS (SELECT 1 FROM DMatch WHERE elem = tmatch_elem) OR (Sing = TRUE)
      THEN
        BEGIN
          INSERT INTO DMatch VALUES (DEFAULT, tmatch_id1, tmatch_id2, tmatch_elem, tmatch_color, tmatch_four, tmatch_beam, tmatch_date);
          EXCEPTION
          WHEN unique_violation THEN
          -- ne rien faire.
        END;

        IF (TEMP = 1) THEN
          INSERT INTO DMatchHierarchy VALUES (Id, 0, tmatch_elem);
        END IF;
        IF (TEMP = 2) THEN
          INSERT INTO DMatchHierarchy VALUES (Id, tmatch_id1, tmatch_elem);
        END IF;

        Id := Id + 1;
      ELSE
        IF (TEMP = 1) THEN
          SELECT match_id INTO tmatch_id1 FROM DMatch WHERE elem = tmatch_elem;
        END IF;
        IF (TEMP = 2) THEN
          SELECT match_id INTO tmatch_id2 FROM DMatch WHERE elem = tmatch_elem;
        END IF;
      END IF;

      TEMP := TEMP + 1;
      Sing := FALSE;
    END LOOP;
    tmatch_id1 := NULL;
    tmatch_id2 := NULL;
    tmatch_elem := NULL;
    tmatch_color := NULL;
    tmatch_four := NULL;
    tmatch_beam := NULL;
    tmatch_date := NULL;
    Temp := 1;
  END LOOP;
END;
$$ LANGUAGE plpgsql;