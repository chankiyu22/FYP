#ifndef DOC_H
#define DOC_H

#include <fstream>
#include <iostream>
#include "./Vocab.h"
#include "./Intstr.h"
using namespace std;

class Document {
public:
  Document(Vocab* vocab, char* filename);
  void count(int num_docs);

private:
  ifstream ifs;
  ofstream ofs;
  Vocab* vocab;
};

#endif
