OBJ = vocabextract.o Intstr.o Str_cell.o Vocab.o Document.o porter.o
OUT = vocabextract

all: $(OBJ)
	g++ -o $(OUT) $(OBJ)

vocabextract.o: vocabextract.cpp Intstr.h 
	g++ -c vocabextract.cpp

Intstr.o: Intstr.cpp Intstr.h Str_cell.h
	g++ -c Intstr.cpp

Str_Cell.o: Str_cell.cpp Str_cell.cpp
	g++ -c Str_cell.cpp

Vocab.o: Vocab.cpp Intstr.h ./porter/porter.h
	g++ -c Vocab.cpp

Document.o: Document.cpp Intstr.h
	g++ -c Document.cpp

porter.o: ./porter/porter.cpp ./porter/porter.h
	g++ -c ./porter/porter.cpp

clear: 
	rm -f $(OUT) $(OBJ) 


