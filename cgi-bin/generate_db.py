import sys
import math
from datetime import datetime
import sqlite


### score functions ###

def get_doc_score(doca, docb):
    score = 0
    total = 0
    for topic_id in range(len(doca)):
        thetaa = doca[topic_id]
        thetab = docb[topic_id]
        if not ((thetaa != 0.0 and thetab == 0.0) or (thetaa == 0.0 and thetab != 0.0)):
            score += math.pow(thetaa - thetab, 2)
    return 0.5 * score

def get_topic_score(topica, topicb):
    score = 0
    total = math.pow(abs(math.sqrt(100) - math.sqrt(0)), 2) * len(topica)
    for term_id in range(len(topica)):
        thetaa = abs(topica[term_id])
        thetab = abs(topicb[term_id])
        score += math.pow(abs(math.sqrt(thetaa) - math.sqrt(thetab)), 2)
    return 0.5 * score / total

def get_term_score(terma, termb):
    score = 0
    for term_id in range(len(terma)):
        score += math.pow(terma[term_id] - termb[term_id], 2)
    return score


### write relations to db functions ###

def write_doc_doc(con, cur, gamma_file):
    cur.execute('CREATE TABLE doc_doc (id INTEGER PRIMARY KEY, doc_a INTEGER, doc_b INTEGER, score FLOAT)')
    cur.execute('CREATE INDEX doc_doc_idx1 ON doc_doc(doc_a)')
    cur.execute('CREATE INDEX doc_doc_idx2 ON doc_doc(doc_b)')
    con.commit()

    # for each line in the gamma file
    read_file = file(gamma_file, 'r')
    docs = []
    for doc in read_file:
        docs.append(map(float, doc.split()))
    read_file.close()
    for i in range(len(docs)):
        for j in range(len(docs[i])):
            docs[i][j] = math.pow(abs(docs[i][j]), 2)
    
    print len(docs)
    for a in range(len(docs)):
        if a % 100 == 0:
            print "doc " + str(a)
        doc_by_doc = {}
        for b in range(a, len(docs)):
            score = get_doc_score(docs[a], docs[b])
            if score == 0:
                continue
            elif len(doc_by_doc) < 10:
                doc_by_doc[score] = (a, b)
            else:
                max_score = max(doc_by_doc.keys())
                if max_score > score:
                    del doc_by_doc[max_score]
                    doc_by_doc[score] = (a, b)
        
        for doc in doc_by_doc:
            execution_string = 'INSERT INTO doc_doc (id, doc_a, doc_b, score) VALUES(NULL, %s, %s, %s)'
            cur.execute(execution_string, [str(doc_by_doc[doc][0]), str(doc_by_doc[doc][1]), str(doc)])

    con.commit()

def write_doc_topic(con, cur, gamma_file):
    cur.execute('CREATE TABLE doc_topic (id INTEGER PRIMARY KEY, doc INTEGER, topic INTEGER, score FLOAT)')
    cur.execute('CREATE INDEX doc_topic_idx1 ON doc_topic(doc)')
    cur.execute('CREATE INDEX doc_topic_idx2 ON doc_topic(topic)')
    con.commit()

    # for each line in the gamma file
    doc_no = 0
    for doc in file(gamma_file, 'r'):
        doc = map(float, doc.split())
        for i in range(len(doc)):
            cur.execute('INSERT INTO doc_topic (id, doc, topic, score) VALUES(NULL, %d, %d, %f)', [doc_no, i, doc[i]])
        doc_no = doc_no + 1

    con.commit()
        

def write_topics(con, cur, beta_file, vocab):
    cur.execute('CREATE TABLE topics (id INTEGER PRIMARY KEY, title VARCHAR(100), views INTEGER, up INTEGER, down INTEGER)')
    con.commit()

    # for each line in the beta file
    indices = range(len(vocab))
    topics_file = open(filename, 'a')
    for topic in file(beta_file, 'r'):
        topic = map(float, topic.split())
	indices = range(len(topic))
        indices.sort(lambda x,y: -cmp(topic[x], topic[y]))
        cur.execute('INSERT INTO topics (id, title, views, up, down) VALUES(NULL, %s, %d, %d, %d)', ["{" + vocab[indices[0]] + ', ' + vocab[indices[1]] + ', ' + vocab[indices[2]] + "}", 0, 0, 0])
    con.commit()

    
def write_topic_term(con, cur, beta_file):
    cur.execute('CREATE TABLE topic_term (id INTEGER PRIMARY KEY, topic INTEGER, term INTEGER, term_title VARCHAR(100), score FLOAT)')
    cur.execute('CREATE INDEX topic_term_idx1 ON topic_term(topic)')
    cur.execute('CREATE INDEX topic_term_idx2 ON topic_term(term)')
    cur.execute('CREATE TRIGGER add_term_title AFTER INSERT ON topic_term FOR EACH ROW BEGIN UPDATE topic_term SET term_title = (SELECT title FROM terms WHERE term = terms.id-1); END;')
    con.commit()
    
    topic_no = 0
    topic_term_file = open(filename, 'a')
    
    for topic in file(beta_file, 'r'):
        print '  Topic ' + str(topic_no)
        topic = map(float, topic.split())
        indices = range(len(topic)) # note: len(topic) should be the same as len(vocab)

        indices.sort(lambda x,y: -cmp(topic[x], topic[y]))
        for i in range(len(topic)):
            if topic[indices[i]] > -6.0: 
            	cur.execute('INSERT INTO topic_term (id, topic, term, score) VALUES(NULL, %d, %d, %f)', [topic_no, indices[i], topic[indices[i]]])
        topic_no = topic_no + 1

    con.commit()


def write_topic_topic(con, cur, beta_file):
    cur.execute('CREATE TABLE topic_topic (id INTEGER PRIMARY KEY, topic_a INTEGER, topic_b INTEGER, score FLOAT)')
    cur.execute('CREATE INDEX topic_topic_idx1 ON topic_topic(topic_a)')
    cur.execute('CREATE INDEX topic_topic_idx2 ON topic_topic(topic_b)')
    con.commit()
    # for each line in the beta file
    read_file = file(beta_file, 'r')
    topics = []
    topic_no = 0
    for topic in read_file:
        topics.append(map(float, topic.split()))
        topic_no = topic_no +1
    
    topica_count = 0
    topic_by_topic = []
    for topica in topics:
        #topic_sim = r
        topicb_count = 0
        for topicb in topics:
            if topic_by_topic.count((topicb_count, topica_count)) != 0:
                topicb_count +=1
                continue
            score = get_topic_score(topica, topicb)
            cur.execute('INSERT INTO topic_topic (id, topic_a, topic_b, score) VALUES(NULL, %d, %d, %f)', [topica_count, topicb_count, score])
            
            topic_by_topic.append((topica_count, topicb_count))
            topicb_count = topicb_count + 1
        topica_count = topica_count + 1

    con.commit()



def write_term_term(con, cur, beta_file, no_vocab):
    v = {}
    
    cur.execute('CREATE TABLE term_term (id INTEGER PRIMARY KEY, term_a INTEGER, term_b INTEGER, score FLOAT)')
    cur.execute('CREATE INDEX term_term_idx1 ON term_term(term_a)')
    cur.execute('CREATE INDEX term_term_idx2 ON term_term(term_b)')
    con.commit()
    
    for i in range(no_vocab):
	    v[i] = []

    for topic in file(beta_file, 'r'):
        topic = map(float, topic.split())
        for i in range(no_vocab):
            v[i].append(math.sqrt(math.exp(topic[i])))
    
    for terma in range(no_vocab):
        if terma % 100 == 0:
            print "term " + str(terma) + "/" + str(no_vocab)
        term_by_term = {}
        for termb in range(terma, len(vocab)):
            score = get_term_score(v[terma], v[termb])
            if score == 0:
                continue
            elif len(term_by_term) < 10:
                term_by_term[score] = (terma, termb)
            else:
                max_score = max(term_by_term.keys())
                if max_score > score:
                    del term_by_term[max_score]
                    term_by_term[score] = (terma, termb)
        
        for term in term_by_term:
            execution_string = 'INSERT INTO term_term (id, term_a, term_b, score) VALUES(NULL, %d, %d, %f)'
            cur.execute(execution_string, [term_by_term[term][0], term_by_term[term][1], term])

    con.commit()
    
def write_doc_term(con, cur, wordcount_file, no_words):
    cur.execute('CREATE TABLE doc_term (id INTEGER PRIMARY KEY, doc INTEGER, term INTEGER, score FLOAT)')
    cur.execute('CREATE INDEX doc_term_idx1 ON doc_term(doc)')
    cur.execute('CREATE INDEX doc_term_idx2 ON doc_term(term)')
    con.commit()
    
    doc_no = 0
    for doc in file(wordcount_file, 'r'):
        print "  Document " + str(doc_no)
        doc = doc.split()[1:]
        terms = {}
        for term in doc:
            terms[int(term.split(':')[0])] = int(term.split(':')[1])

        for i in range(no_words):
            score = 0
            if terms.has_key(i):
                score = terms[i]
                execution_string = 'INSERT INTO doc_term (id, doc, term, score) VALUES(NULL, %d, %d, %f)'
                cur.execute(execution_string, [doc_no, i, score])

        doc_no += 1
    
    con.commit()

def write_terms(con, cur, terms_file):
    cur.execute('CREATE TABLE terms (id INTEGER PRIMARY KEY, title VARCHAR(100), up INTEGER, down INTEGER)')
    con.commit()

    for line in open(terms_file, 'r'):
        cur.execute('INSERT INTO terms (id, title, up, down) VALUES (NULL, %s, %d, %d)', [line.strip(), 0, 0])

    con.commit()

def write_docs(con, cur, docs_file):
    cur.execute('CREATE TABLE docs (id INTEGER PRIMARY KEY, title VARCHAR(100), date DATE, stars INTEGER, funny INTEGER, useful INTEGER, cool INTEGER, views INTEGER, up INTEGER, down INTEGER)')
    con.commit()

    for line in open(docs_file, 'r'):
        votes = [0, 0, 0]
        splitted_line = line.split('", "')

        text_tag = splitted_line[3]
        vote_tag = splitted_line[0].split('}, ')[0].strip('{')
        star_tag = splitted_line[2].split(', ')[0]

        text = text_tag.split('": "')[1]
        text = text.replace("\\n", " ")
        text = text.replace("\\\"", "\"")
        vote_info = vote_tag.split(': {')[1].split(', ')
        for i in range(0, 3):
            votes[i] = vote_info[i].split('": ')[1]
        stars = star_tag.split('": ')[1]

        date = splitted_line[2].split('"date": ')[1].strip('"')

        cur.execute('INSERT INTO docs (id, title, date, stars, funny, useful, cool, views, up, down) VALUES(NULL, %s, %s, %s, %s, %s, %s, %d, %d, %d)', [text[0:50], date, stars, votes[0], votes[1], votes[2], 0, 0, 0])

    con.commit()

def create_comment_tables(con, cur):
    cur.execute('CREATE TABLE index_comment (id integer primary key, comment varchar(100), time datetime default current_timestamp)')

    cur.execute('CREATE TABLE term_comment ( id integer primary key, term integer, comment varchar(100), time datetime default current_timestamp, foreign key(term) references terms(id) )')

    cur.execute('CREATE TABLE topic_comment ( id integer primary key, topic integer, comment varchar(100), time datetime default current_timestamp, foreign key(topic) references topics(id) )')

    con.commit()


### main ###

if (__name__ == '__main__'):
    if (len(sys.argv) != 7):
       print 'usage: python generate_csvs.py <db-filename> <doc-wordcount-file> <beta-file> <gamma-file> <vocab-file> <doc-file>\n'
       sys.exit(1)

    filename = sys.argv[1]
    doc_wordcount_file = sys.argv[2]
    beta_file = sys.argv[3]
    gamma_file = sys.argv[4]
    vocab_file = sys.argv[5]
    doc_file = sys.argv[6]

    # connect to database, which is presumed to not already exist
    con = sqlite.connect(filename)
    cur = con.cursor()

    # pre-process vocab, since several of the below functions need it in this format
    vocab = file(vocab_file, 'r').readlines()
    vocab = map(lambda x: x.strip(), vocab)

    # write the relevant rlations to the database, see individual functions for details
    print "writing terms to db..."
    write_terms(con, cur, vocab_file)
    
    print "writing docs to db..."
    write_docs(con, cur, doc_file)
    
    print "writing doc_doc to db..."
    write_doc_doc(con, cur, gamma_file)

    print "writing doc_topic to db..."
    write_doc_topic(con, cur, gamma_file)

    print "writing topics to db..."
    write_topics(con, cur, beta_file, vocab)

    print "writing topic_term to db..."
    write_topic_term(con, cur, beta_file)

    print "writing topic_topic to db..."
    write_topic_topic(con, cur, beta_file)
    
    print "writing term_term to db..."
    write_term_term(con, cur, beta_file, len(vocab))
    
    print "writing doc_term to db..."
    write_doc_term(con, cur, doc_wordcount_file, len(vocab))

    print "finalizing comments"
    create_comment_tables(con, cur)
