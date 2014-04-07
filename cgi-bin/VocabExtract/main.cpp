#include <iostream>
#include <fstream>
#include <locale>
#include "Intstr.h"
#include "./VocabExtract/vocabextract.h"
#include "./CountOnReview/Count_Review.h"
using namespace std;

Intstr* vocabExtract(ifstream& ifs);

int main(int argc, char* argv[]) {
  if (argc == 1)
    return 0;

  ifstream ifs;
  ifs.open(argv[1]);

  cout << "Analysing Vocabs..." << endl;

  Intstr* intstr = NULL;
  intstr = extractVocab(ifs);

  printVocabs(intstr);
  ifs.close();

  if (argc == 2)
    return 0;

  cout << "Analysing Documents..." << endl;

  /** TODO: Read File 'Review.json', extract words and count **/
  ifs.open(argv[2]);
  countVocabs(ifs, intstr);
  
  return 0;
}
