CXX      := -g++
CXXFLAGS := -pedantic-errors -Wall -Wextra -DDEBUG -g -O2
ALGO     := -DALGO_
LDFLAGS  := -L/usr/lib -lstdc++ -lm -L./lib/
OBJ_DIR  := ./build
APP_DIR  := ./exe
TARGET   := taged
INCLUDE  := -Iinclude/ -Ilib/boost/
SRC      :=                      \
   $(wildcard src/*.cpp)         \

ALGO_NAME:=E
f: ALGO_NAME=F
c: ALGO_NAME=C



OBJECTS  := $(SRC:%.cpp=$(OBJ_DIR)/%.o)
DEPENDENCIES \
         := $(OBJECTS:.o=.d)

all: algo

f: algo
c: algo

algo: clean build $(APP_DIR)/$(TARGET)$(ALGO_NAME)

$(OBJ_DIR)/%.o: %.cpp
	@mkdir -p $(@D)
	$(CXX) $(CXXFLAGS) $(ALGO)$(ALGO_NAME) $(INCLUDE) -c $< -MMD -o $@

$(APP_DIR)/$(TARGET)$(ALGO_NAME): $(OBJECTS)
	@mkdir -p $(@D)
	$(CXX) $(CXXFLAGS)  -o $(APP_DIR)/$(TARGET)$(ALGO_NAME) $^ $(LDFLAGS)

-include $(DEPENDENCIES)

build:
	@mkdir -p $(APP_DIR)
	@mkdir -p $(OBJ_DIR)

clean:
	-@rm -rvf $(OBJ_DIR)/*
#	-@rm -rvf $(APP_DIR)/*

info:
	@echo "[*] Application dir: ${APP_DIR}     "
	@echo "[*] Object dir:      ${OBJ_DIR}     "
	@echo "[*] Sources:         ${SRC}         "
	@echo "[*] Objects:         ${OBJECTS}     "
	@echo "[*] Dependencies:    ${DEPENDENCIES}"
