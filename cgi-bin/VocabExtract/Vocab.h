#ifndef VOCAB_H
#define VOCAB_H

#include <fstream>
#include <iostream>
#include <string>
#include <vector>
#include <ctype.h>
#include "./porter/porter.h"
#include "./Intstr.h"
using namespace std;

class Vocab {
public:
  Vocab();
  void setExclusion(char* filename);
  void extractVocab(char* filename, int num_docs);
  void printToFile(char* filename);
  void initCount();
  void printCountToFile(ofstream& ofs);
  void addCount(string s);

private:
  bool exclusionExists(char* s);
  Intstr* vocab_list;
  vector<string> exclusion_list;
  int* counter;
  int numvocab;

  // TODO
  int getNumVocab();
  int getExistVocab();
};

string  normalize(string s);

#endif
